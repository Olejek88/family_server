<?php

namespace frontend\models;

use Yii;
use yii\data\ArrayDataProvider;

class AccessSearch extends AccessModel
{

    private $filterModel = false;
    private $filterPermission = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'permission'], 'string'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */
    public function search($params)
    {
        if ($this->load($params)) {
            $this->filterModel = $this->model != '';
            $this->filterPermission = $this->permission != '';
        }

        $am = Yii::$app->getAuthManager();
        $roles = $am->getRoles();
        $permissions = $am->getPermissions();

        $grouped = [];
        foreach ($permissions as $permission) {
            if ($permission->type == 2) {
                if (preg_match('/([a-z-]*)([A-Z].*)/', $permission->name, $match)) {
                    if ($this->filterModel && $this->filterPermission) {
                        if (preg_match('/' . preg_quote($this->model, '/') . '/i', $match[2]) && preg_match('/' . preg_quote($this->permission, '/') . '/i', $match[1])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } elseif ($this->filterModel && !$this->filterPermission) {
                        if (preg_match('/' . preg_quote($this->model, '/') . '/i', $match[2])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } elseif (!$this->filterModel && $this->filterPermission) {
                        if (preg_match('/' . preg_quote($this->permission, '/') . '/i', $match[1])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } else {
                        $grouped[$match[2]][$match[1]] = $permission->name;
                    }
                }
            }
        }

        $permsByRole = [];
        foreach ($roles as $role) {
            $permsByRole[$role->name] = $am->getPermissionsByRole($role->name);
        }

        $data = [];
        $idx = 0;
        foreach ($grouped as $model => $permission) {
            foreach ($permission as $shortName => $value) {
                $accessModel = new AccessModel();
                $accessModel->id = $idx++;
                $accessModel->model = $model;
                $accessModel->permission = $shortName;
                foreach ($permsByRole as $name => $role) {
                    if (isset($role[$value])) {
                        $roleName = $name;
                        $accessModel->$roleName = true;
                    }
                }
                $data[] = $accessModel;
            }
        }

        unset($roles);
        unset($permissions);
        unset($permsByRole);

        $dataProvider = new ArrayDataProvider();
        $dataProvider->allModels = $data;
        $dataProvider->pagination = [
            'pageSize' => 20,
        ];
        return $dataProvider;
    }
}

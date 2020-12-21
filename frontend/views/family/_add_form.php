<?php
/* @var $model Family */

use common\components\MainFunctions;
use common\models\Family;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form-object',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить') ?></h4>
</div>
<div class="modal-body">
    <?php
    if ($model['uuid']) {
        echo $form->field($model, 'uuid')
            ->hiddenInput(['value' => $model['uuid']])
            ->label(false);
    } else {
        echo $form->field($model, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }
    echo $form->field($model, 'deleted')->hiddenInput(['value' => 0])->label(false);
    echo $form->field($model, 'title')->textInput(['maxlength' => true]);
    ?>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    let send = false;
    $(document).on("beforeSubmit", "#form-object", function (e) {
        e.preventDefault();
    }).on('submit', '#form-object', function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                type: "post",
                data: $('#form-object').serialize(),
                url: "../family/save",
                success: function () {
                    $('#modalAdd').modal('hide');
                },
                error: function () {
                }
            });
        }
    });
</script>
<?php ActiveForm::end(); ?>

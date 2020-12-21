<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var array $objects */
/* @var $sq string */

$this->title = Yii::t('app', 'Family Tree');
?>
    <table id="tree" style="width: 100%">
        <colgroup>
            <col width="*">
            <col width="200px">
            <col width="120px">
            <col width="120px">
            <col width="90px">
        </colgroup>
        <thead style="color: white" class="thead_tree">
        <tr>
            <th style="vertical-align: center" colspan="1">
                <table>
                    <tr>
                        <td>
                            <?=
                            Html::a('<button class="btn btn-success" type="button" id="addButton" style="padding: 1px 7px"
                            title="Добавить объект"><span class="fa fa-plus" aria-hidden="true"></span></button>',
                                ['new', 'root' => true],
                                ['data-toggle' => 'modal', 'data-target' => '#modalAdd']);
                            ?>
                        </td>
                        <td style="padding-left: 5px; padding-right: 5px">
                            <input name="search" placeholder="..." autocomplete="off" class="form-control"
                                   style="background-color: white"/>
                        </td>
                        <td style="padding-right: 5px">
                            <button id="btnResetSearch" style="padding: 1px 5px">&times;</button>
                        </td>
                        <td>
                            <span id="matches"></span>
                        </td>
                    </tr>
                </table>
            </th>
            <th align="center" colspan="6"><?php echo Yii::t('app', 'Family Tree') ?></th>
        </tr>
        <tr>
            <th align="center"><?php echo Yii::t('app', 'Family') ?></th>
            <th><?php echo Yii::t('app', 'e-mail') ?></th>
            <th><?php echo Yii::t('app', 'Longitude') ?></th>
            <th><?php echo Yii::t('app', 'Latitude') ?></th>
            <th><?php echo Yii::t('app', 'S') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td class="alt"></td>
            <td class="center"></td>
            <td class="alt"></td>
        </tr>
        </tbody>
    </table>
    <div class="modal remote fade" id="modalAdd">
        <div class="modal-dialog" style="width: 800px; height: 400px">
            <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
            </div>
        </div>
    </div>
<?php
$this->registerJsFile('/js/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/ui.fancytree.css');
$this->registerCssFile('/css/jquery.contextMenu.min.css');

echo FancytreeWidget::widget(
    [
        'options' => [
            'id' => 'tree',
            'source' => $objects,
            'keyboard' => false,
            'checkbox' => true,
            'selectMode' => 2,
            'quicksearch' => true,
            'autoScroll' => true,
            'extensions' => ['dnd', 'table', 'contextMenu', 'filter'],
            'dnd' => [
                'preventVoidMoves' => true,
                'preventRecursiveMoves' => false,
                'preventSameParent' => false,
                'preventRecursion' => false,
                'autoExpandMS' => 400,
                'dragStart' => new JsExpression('function(node, data) {                                                                                                                                     
                                return true;                                                                                                                                                                
                        }'),
                'dragEnter' => new JsExpression('function(node, data) {                                                                                                                                     
                                return true;                                                                                                                                                                
                        }'),
                'dragDrop' => new JsExpression('function(node, data) {                                                                                                                                      
                    if (data.otherNode.selected) {                                                                                                                                              
                        $.ajax({                                                                                                                                                                
                            url: "../family/user-copy",                                                                                                                                                 
                            type: "post",                                                                                                                                                                       
                            data: {                                                                                                                                                         
                                from: data.otherNode.data.email,                                                                                                                                 
                                to: node.key                                                                                                                                     
                        },                                                                                                                                                                                  
                        success: function (code) {                                                                                                                                                          
                            var message = JSON.parse(code);                                                                                                                                                 
                                 if (message.code == 0) {                                                                                                                                        
                                      newNode = data.otherNode.copyTo(node, data.hitMode);                                                                                                            
                                      console.log(message);                                                                                                                                           
                                      newNode.data.uuid = message.message.uuid;                                                                                                                       
                                      newNode.data.key = message.message._id;                                                                                                                         
                                      newNode.key = message.message._id;                                                                                                                              
                                      newNode.setTitle(message.message.title);                                                                                                                        
                                      newNode.data = message.message.data;                                                                                                                            
                                      newNode.render(true);                                                                                                                                           
                                 }                                                                                                                                                               
                                 else {                                                                                                                                                          
                                      alert (message.message);                                                                                                                                    
                                 }                                                                                                                                                               
                        }                                                                                                                                                                                   
                    });                                                                                                                                                                                     
                }                                                                                                                                                                                           
                        }'),
            ],
            'contextMenu' => [
                'menu' => [
                    'delete' => [
                        'name' => Yii::t('app', 'Удалить'),
                        'icon' => "delete",
                        'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                var node = $.ui.fancytree.getNode(opt.$trigger);
                                console.log(node.data.uuid);
                                $.ajax({
                                      url: "deleted",
                                      type: "post",
                                      data: {
                                          selected_node: data.key,
                                          folder: data.folder,
                                          type: node.type,
                                          uuid: node.data.uuid
                                      },
                                      success: function (code) {
                                        var message = JSON.parse(code);
				                        if (message.code === 0) {
                                            data.remove();
                                        } else {
                                            alert (message.message);
                                        }            
                                      }                                    
                                   });
                            });
                         }')
                    ]
                ]
            ],
            'table' => [
                'indentation' => 20,
                "titleColumnIdx" => "1",
                "emailColumnIdx" => "2",
                "longitudeColumnIdx" => "3",
                "latitudeColumnIdx" => "4",
                "linksColumnIdx" => "5",
            ],
            'renderColumns' => new JsExpression(
                'function(event, data) {
                    var node = data.node;
                    $tdList = $(node.tr).find(">td");
                    $tdList.eq(1).html(node.data.email);           
                    $tdList.eq(2).html(node.data.latitude);           
                    $tdList.eq(3).html(node.data.longitude);
                    $tdList.eq(4).html(node.data.links);
                }'
            )
        ]
    ]
);
?>
<?php
$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalParameter").modal("hide");
})');
$this->registerJs('$("input[name=search]").on("keyup", function(e){
    var n = 0,
        tree = $.ui.fancytree.getTree(),
        args = "autoApply autoExpand fuzzy hideExpanders highlight leavesOnly nodata mode".split(" "),
        opts = {},
        filterFunc = $("#branchMode").is(":checked") ? tree.filterBranches : tree.filterNodes,
        match = $(this).val();

      $.each(args, function(i, o) {
          opts[o] = $("#" + o).is(":checked");
      });
      opts.mode = "hide";
      opts.autoExpand = true;

      if(e && e.which === $.ui.keyCode.ESCAPE || $.trim(match) === ""){
          $("button#btnResetSearch").click();
          return;
      }
      
      tree.filterNodes(function(node){
            let matchL = match.toLowerCase();
            if (node.title && node.title.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
                return true;
            }
            if (node.data.inventory && node.data.inventory.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
                return true;
            }
            if (node.data.serialText && node.data.serialText.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
               return true;
            }
      },opts);

      $("button#btnResetSearch").attr("disabled", false);
      $("span#matches").text("(" + n + ")");
    }).focus();
');
$this->registerJs('$("button#btnResetSearch").click(function(e){
      $("input[name=search]").val("");
      $("span#matches").text("");
      $.ui.fancytree.getTree().clearFilter();
    }).attr("disabled", true);
');
$this->registerJs('
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === "node") {
                $.ui.fancytree.getTree().activateKey(decodeURIComponent(tmp[1]));
            }            
        });
');
$this->registerJs('$("#modalAdd").on("show.bs.modal",
function () {
    var w0 = document.getElementById(\'w0\');
    if (w0) {
      w0.id = \'w1\';
    }
})');
?>
<?php
/** @var $stack */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Stack') ?></h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tbody>
        <tr>
            <td><?= $stack ?></td>
        </tr>
        </tbody>
    </table>
</div>

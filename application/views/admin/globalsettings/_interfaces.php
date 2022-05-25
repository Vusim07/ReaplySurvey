<?php
/**
* This view generate the interface tab inside global settings.
*
*/
?>

<?php $RPCInterface=getGlobalSetting('RPCInterface'); ?>
<div class="form-group">
    <label class=" form-label"  for='RPCInterface'><?php eT("RPC interface enabled:"); ?></label>
    <div>
        <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
            'name'          => 'RPCInterface',
            'checkedOption' => $RPCInterface,
            'selectOptions' => [
                "off"  => gT("Off", 'unescaped'),
                "json" => gT("JSON-RPC", 'unescaped'),
                "xml"  => gT("XML-RPC", 'unescaped')
            ]
        ]); ?>
    </div>
</div>

<div class="form-group">
    <label class=" form-label" ><?php eT("URL:"); ?></label>
    <div class="">
        <?php echo $this->createAbsoluteUrl("admin/remotecontrol"); ?>
    </div>
</div>

<div class="form-group">
    <label class=" form-label"  for='rpc_publish_api'><?php eT("Publish API on /admin/remotecontrol:"); ?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
            'name' => 'rpc_publish_api',
            'id'=>'rpc_publish_api',
            'value' => getGlobalSetting('rpc_publish_api'),
            'onLabel'=>gT('On'),
            'offLabel' => gT('Off')));
        ?>
    </div>
</div>

<div class="form-group">
    <label class=" form-label"  for='add_access_control_header'><?php eT("Set Access-Control-Allow-Origin header:"); ?></label>
    <div class="">
        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
            'name' => 'add_access_control_header',
            'id'=>'add_access_control_header',
            'value' => getGlobalSetting('add_access_control_header'),
            'onLabel'=>gT('On'),
            'offLabel' => gT('Off')));
        ?>
    </div>
</div>

<?php if (Yii::app()->getConfig("demoMode")==true):?>
    <p><?php eT("Note: Demo mode is activated. Marked (*) settings can't be changed."); ?></p>
    <?php endif; ?>

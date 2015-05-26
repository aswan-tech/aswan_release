<?php
class FCM_CustomerExport_Model_Source_Modes
{
    public function toOptionArray()
    {
        return array(
            array('value' => FCM_CustomerExport_Model_Reportexport::MODE_FULL, 'label' => 'Full'),
            array('value' => FCM_CustomerExport_Model_Reportexport::MODE_CHANGES, 'label' => 'Changes Only'),
        );
    }
}
<?php

class Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpoints';
        $this->_controller = 'adminhtml_rewardpoints';
        
        $this->_updateButton('save', 'label', Mage::helper('rewardpoints')->__('Import'));
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
    	return Mage::helper('rewardpoints')->__('Import Referral Reward Points');
    }
}
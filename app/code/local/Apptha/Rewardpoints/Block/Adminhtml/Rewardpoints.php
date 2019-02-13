<?php
class Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rewardpoints';
    $this->_blockGroup = 'rewardpoints';
    $this->_headerText = Mage::helper('rewardpoints')->__('Manage Referral Reward Points');
    parent::__construct();
    $this->_removeButton('add');$this->_removeButton('add');
    $this->_addButton('import', array(
            'label'     => Mage::helper('rewardpoints')->__('Import Referral Reward Points'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
            'class'     => 'add',
        ));
    
  }
}
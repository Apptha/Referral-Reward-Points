<?php

class Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('rewardpoints_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('rewardpoints')->__('Manage Referral Reward Points'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('rewardpoints')->__('Import Referral Reward Points'),
          'title'     => Mage::helper('rewardpoints')->__('Import Referral Reward Points'),
          'content'   => $this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
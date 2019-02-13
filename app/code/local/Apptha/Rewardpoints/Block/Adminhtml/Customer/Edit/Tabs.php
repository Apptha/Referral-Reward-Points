<?php
class Apptha_Rewardpoints_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $this->addTab('rewardpoints', array(
            'label'     => Mage::helper('rewardpoints')->__('Referral Reward Points'),
            'content'   => $this->getLayout()->createBlock('rewardpoints/adminhtml_customer_edit_tab_rewardpoints')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));
        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }
}

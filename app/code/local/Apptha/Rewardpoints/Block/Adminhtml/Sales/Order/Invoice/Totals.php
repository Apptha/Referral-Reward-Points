<?php
class Apptha_Rewardpoints_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{

    protected function _initTotals()
    {
		parent::_initTotals();
    	$rewardpoints = Mage::getModel('rewardpoints/rewardpointsorder')->load($this->getOrder()->getId());
		if($rewardpoints->getMoney())
            $this->_totals['rewardpoints_discount'] = new Varien_Object(array(
                'code'      => 'rewardpoints_discount',
                'value'     => $rewardpoints->getMoney(),
                'base_value'=> $rewardpoints->getMoney(),
                'label'     => 'Reward Points Discount'
            ));
        return $this;
    }
}

<?php
class Apptha_Rewardpoints_Model_Order_Invoice_Total_Rewardpoints extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order = $invoice->getOrder();
		$money = Mage::getModel('rewardpoints/rewardpointsorder')->load($order->getId())->getMoney();
		if(!$money) $money = Mage::getSingleton('checkout/session')->getDiscount();
        $totalDiscountAmount     = $money;
        $baseTotalDiscountAmount = $money;
        

        $items = $invoice->getAllItems();
    	if (!count($items)) {
            return $this;
        }
        $invoice->setBaseDiscountAmount($invoice->getBaseDiscountAmount()+$baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);
        
        return $this;
    }


}

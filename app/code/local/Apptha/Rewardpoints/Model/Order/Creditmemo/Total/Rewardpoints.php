<?php
class Apptha_Rewardpoints_Model_Order_Creditmemo_Total_Rewardpoints extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

        $order = $creditmemo->getOrder();

        $money = Mage::getModel('rewardpoints/rewardpointsorder')->load($order->getId())->getMoney();
		
        $totalDiscountAmount     = $money;
        $baseTotalDiscountAmount = $money;
		
    	$items = $creditmemo->getAllItems();
    	if (!count($items)) {
            return $this;
        }
        $creditmemo->setBaseDiscountAmount($creditmemo->getBaseDiscountAmount()+$baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        return $this;
    }
}

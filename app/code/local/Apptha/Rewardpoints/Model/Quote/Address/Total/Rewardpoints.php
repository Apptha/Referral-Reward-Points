<?php
class Apptha_Rewardpoints_Model_Quote_Address_Total_Rewardpoints extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct(){
        $this->setCode('reward_points');
    }
	public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
    	$quote = $address->getQuote();
        $totalDiscountAmount = Mage::getSingleton('checkout/session')->getDiscount();
        $subtotalWithDiscount= 0;
        $baseTotalDiscountAmount = Mage::getSingleton('checkout/session')->getDiscount();
        $baseSubtotalWithDiscount= 0;
    	$items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $address->setRewardPointsDiscount($totalDiscountAmount);
        $address->setSubtotalWithDiscount($subtotalWithDiscount - $totalDiscountAmount);
        $address->setBaseRewardPointsDiscount($baseTotalDiscountAmount);
        $address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount - $baseTotalDiscountAmount);
		
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getRewardPointsDiscount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-$address->getBaseRewardPointsDiscount());
        $address->setBaseDiscountAmount($address->getBaseDiscountAmount()-$address->getBaseRewardPointsDiscount());
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getRewardPointsDiscount();
        if ($amount!=0) {
            $title = Mage::helper('sales')->__('Reward Points Discount');
            
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$title,
                'value'=>-$amount
            ));
        }
        return $this;
    }
    

}

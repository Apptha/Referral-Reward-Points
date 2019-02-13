<?php
class Apptha_Rewardpoints_Block_Checkout_Cart_Rewardpoints extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
    	return parent::_prepareLayout();
    }
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    protected function _getCustomer()
    {
    	return Mage::getSingleton('customer/session')->getCustomer();
    }
    protected function _getQuote()
    {
    	return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    protected function roundPoints($points,$up = true)
    {
		$rate = $this->getPointPerMoney();
		$tmp = (int)($points/$rate[0]) * $rate[0];
		if($up)
			return $tmp<$points?$tmp+$rate[0]:$tmp;
		return $tmp;
    }
    
    public function getRewardPoints()
    {
    	return Mage::getSingleton('checkout/session')->getRewardPoints();
    }
	
    public function getPointPerMoney()
	{
		$config = Mage::getStoreConfig('rewardpoints/general/points_currency_rate');
		$rate = explode("/",$config);
		return $rate;
	}
	
	public function formatMoney($money)
	{
		return Mage::helper('core')->currency($money);
	}
	
	public function getCurrentRewardPoints()
	{
		$customer = Mage::getModel('rewardpoints/customer')->load($this->_getCustomer()->getId());
		return $customer->getAppthaRewardPoint();
	}
	
	public function getRewardPointsRule()
	{
		return Mage::helper('rewardpoints')->getCheckoutRewardPointsRule($this->_getQuote());
	}
	
	public function getMaxPointsToCheckout()
	{
		if($this->_getQuote()->getCouponCode() && Mage::getStoreConfig('rewardpoints/config/retrict_other_promotions')) return 0;
		$maxPoints = Mage::helper('rewardpoints')->getMaxPointToCheckOut();
		$grandTotal = $this->_getSession()->getQuote()->getGrandTotal()+ Mage::helper('rewardpoints')->exchangePointsToMoneys($this->_getSession()->getRewardPoints());
		$quote = $this->_getQuote();
		if ($quote->isVirtual()) {
    		$address = $quote->getBillingAddress();
    	}else
    	{
    		$address = $quote->getShippingAddress();
    	}
		$subtotal = $address->getTotalAmount('subtotal');
		$discount = $address->getTotalAmount('discount');
		$subtotal += $discount;
		$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($subtotal);
		//$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($grandTotal);
		$customerPoints = $this->getCurrentRewardPoints();
		$tmp = 0;
		if(strpos($maxPoints,"%")){
	    	$percent = str_replace("%","",$maxPoints);
	    	$tmp = $this->roundPoints($percent * $points/100);
	    	return $customerPoints>$tmp?$tmp:$this->roundPoints($customerPoints,false);
	    }else{
	    	if($maxPoints){
		    	$tmp = $this->roundPoints($maxPoints, false);
		    	return ($customerPoints>$points)?($points>$tmp)?$tmp:$this->roundPoints($points):$this->roundPoints($customerPoints,false);
	    	}
	    	//else
	    	$tmp = $this->roundPoints($points);
	    	return $customerPoints>$tmp?$tmp:$this->roundPoints($customerPoints,false);
	    }
	}
	public function formatNumber($value)
	{
		return Mage::helper('rewardpoints')->formatNumber($value);
	}
}
<?php
class Apptha_Rewardpoints_CheckoutController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
	/**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
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
    
	protected function _roundPoints($points,$up = true)
    {
		$config = Mage::getStoreConfig('rewardpoints/general/points_currency_rate');
		$rate = explode("/",$config);
		$tmp = ($points/$rate[0]) * $rate[0];
		if($up)
			return $tmp<$points?$tmp+$rate[0]:$tmp;
		return $tmp;
    }
	/**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        if (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) {

            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
	public function rewardpointscheckoutAction()
    {
    	if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
    	$rewardpoints = $this->getRequest()->getParam('rewardpoints');
    	if($rewardpoints < 0) $rewardpoints = - $rewardpoints;
    	$rate = Mage::helper('rewardpoints')->getPointMoneyRateConfig();    	
    	$rate = explode('/',$rate);    	
    	$rewardpoints = ($rewardpoints/$rate[0]) * $rate[0];       	 
    	if($rewardpoints)
    	{
    		$customer = $this->_getCustomer();
    		if($customer->getId()){
    			$_customer= Mage::getModel('rewardpoints/customer')->load($customer->getId());
    			$maxPoints = Mage::helper('rewardpoints')->getMaxPointToCheckOut();
				$quote = $this->_getQuote();
				if ($quote->isVirtual()) {
		    		$address = $quote->getBillingAddress();
		    	}else
		    	{
		    		$address = $quote->getShippingAddress();
		    	}
		    	
				$subtotal = $address->getTotalAmount('subtotal')?$address->getTotalAmount('subtotal'):$quote->getSubtotal();
				$discount = $address->getTotalAmount('discount')?$address->getTotalAmount('discount'):$quote->getDiscount();
				$subtotal += $discount;
				$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($subtotal);
				
				$customerPoints = $_customer->getRewardPoint();				
				$tmp = 0;
				if(strpos($maxPoints,"%")){
			    	$percent = str_replace("%","",$maxPoints);
			    	$tmp = $this->_roundPoints($percent * $points/100, false);
			    }else{
			    	if($maxPoints){
				    	$tmp = $this->_roundPoints($maxPoints, false);				    	
			    	}else{
				    	$tmp = $this->_roundPoints($points);				    	
			    	}
			    }					      
    			if($rewardpoints <= $tmp && $rewardpoints <= $customerPoints)
		    	{			    		    		
		    		$this->_getSession()->setRewardPoints($rewardpoints);
		    		$money = Mage::helper('rewardpoints')->exchangePointsToMoneys($rewardpoints);
		    		if($money > $subtotal) $money = $subtotal;
		    		$this->_getSession()->setDiscount($money);
		    	}
    		}else{
    			//$this->_getSession()->addError(Mage::helper('rewardpoints')->__('You must login to use this function'));
    		}
    	}else
    	{
    		$this->_getSession()->unsetData('reward_points');
    		$this->_getSession()->unsetData('discount');    		
    	}
    	$this->_getSession()->getQuote()->collectTotals()->save();
    	$this->loadLayout();
		$this->renderLayout();
    }
    
    public function rewardpointsAction()
    {
    	$this->_getSession()->getQuote()->collectTotals()->save();
    	$this->getResponse()->setBody($this->_getSession()->getRewardPoints());
    }
    
    public function updaterulesAction()
    {
    	$this->loadLayout();
		$this->renderLayout();
    }
    
    public function couponPostAction()
    {
    	/**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($couponCode) {
                //retrict other discount
    			if((Mage::getStoreConfig('rewardpoints/config/retrict_other_promotions') && $this->_getQuote()->getCouponCode() && $this->_getSession()->getRewardPoints())) 
    			{
    				$this->_getQuote()->setCouponCode("")->collectTotals()
                ->save();
    				Mage::getSingleton('checkout/session')->addError(Mage::helper("rewardpoints")->__("You already use %s to checkout so you could not use other promotions",Mage::helper('rewardpoints')->getPointCurency()));
    			}else{
	    			if ($couponCode == $this->_getQuote()->getCouponCode()) {
	                    $this->_getSession()->addSuccess(
	                        $this->__('Coupon code "%s" was applied successfully.', Mage::helper('core')->htmlEscape($couponCode))
	                    );
	                }
	                else {
	                    $this->_getSession()->addError(
	                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
	                    );
	                }
    			}
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled successfully.'));
            }

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not apply coupon code.'));
        }

        $this->_goBack();
    }
}
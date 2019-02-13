<?php

class Apptha_Rewardpoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    function __construct()
    {
      $objValidInt = new Zend_Validate_Int();		
    }
	public function isInviteEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/invitations/enabled');
	}
	public function isSocialNetworksEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/invitations/social_networks');
	}
	public function isReferralLinkEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/invitations/referral_link');
	}
	public function isInvFrndViaEmailEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/invitations/invite_friends_via_email');
	}
	public function isFrndRegEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/friend_registration');
	}
	public function getCreditModule()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('Apptha_Credit',$modules))
		{
			if(Mage::getStoreConfig('credit/config/enabled'))
			return true;
		}
		return false;
	}
	public function getPointCurency()
	{
		return $this->__('Reward Points');
	}
	public function formatPoints($points)
	{
		//number_format(number,decimals,decimalpoint,separator)
		$_points = number_format($points,0,'.',',');
		return $_points.' '.$this->getPointCurency();
	}
	public function formatPointsWithoutName($points)
	{
		//number_format(number,decimals,decimalpoint,separator)
		$_points = number_format($points,0,'.',',');
		return $_points;
	}
	public function getCheckoutSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	public function getCurrentCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	public function moduleEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/general/enabled');
	}
	public function isExpDaysEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/general/reward_points_expire_days');
	}
	public function isRevEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/reviewing_product');
	}
	public function isTagEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/tagging_product');
	}
	public function isRegEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/registration');
	}
	public function isPollEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/participating_in_poll');
	}
	public function isPollLimEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/config/poll_limit');
	}
	public function isSubNewLtrEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/signup_newsletter');
	}
	public function isRefLinkEnabled()
	{		
		return Mage::getStoreConfig('rewardpoints/earning_points/referral_link');
	}
	public function isPrdRewdPointEnabled()
	{		
		return Mage::getStoreConfig('rewardpoints/config/enabled_product_reward_point');
	}
	public function isOrdRewdPointEnabled()
	{		
		return Mage::getStoreConfig('rewardpoints/earning_points/points_to_purchasing_order');
	}
	public function formatMoney($money,$format=true, $includeContainer = true)
	{
		return Mage::helper('core')->currency($money,$format, $includeContainer);
	}

	public function exchangePointsToMoneys($rewardpoints)
	{
		$rate = $this->getPointMoneyRateConfig();
		$rate = explode('/',$rate);
		$money = ($rewardpoints * 1.0 * $rate[1])/$rate[0];
		return $money;
	}

	public function exchangeMoneysToPoints($money)
	{
		$rate = $this->getPointMoneyRateConfig();
		$rate = explode('/',$rate);
		$points = ($money * 1.0 * $rate[0]) / $rate[1];
		return $points;
	}

	public function getPointMoneyRateConfig()
	{
		return Mage::getStoreConfig('rewardpoints/general/points_currency_rate');
	}

	public function getMaxPointToCheckOut()
	{
		return Mage::getStoreConfig('rewardpoints/earning_points/maximum_points_to_purchasing_order');
	}

	public function sharePointsToFriend()
	{
		return Mage::getStoreConfig('rewardpoints/share_points/enabled');
	}
	public function enabledCapcha()
	{
		return 1;
	}
	public function getCapchaBackgroundImage()
	{
		return Mage::getDesign()->getSkinBaseDir(array()).DS.'apptha_rewardpoints'.DS.'backgrounds'.DS.'bg3.jpg';
	}
	public function getCapchaBackgroundColor()
	{
		return "#FFFFFF";
	}
	public function getCapchaImageWidth()
	{
		return 255;
	}
	public function getCapchaImageHeight()
	{
		return 50;
	}
	public function getCapchaPerturbation()
	{
		return 0.7;
	}
	public function getCapchaCodeLength()
	{
		return 7;
	}
	public function capchaUseTransparentText()
	{
		return 1;
	}
	public function getCapchaTextTransparencyPercentage()
	{
		return 0;
	}
	public function getCapchaNumberLine()
	{
		return 0;
	}
	public function getCapchaTextColor()
	{
		return '#FF7F27';
	}
	public function getCapchaLineColor()
	{
		return '#E8E8E8';
	}
	public function capchaUseWordList()
	{
		return 0;
	}
	public function allowSendEmailToSender()
	{
		return Mage::getStoreConfig('rewardpoints/share_points/sender_template');
	}

	public function allowSendEmailToRecipient()
	{
		return Mage::getStoreConfig('rewardpoints/share_points/recipient_template');
	}
	public function allowExchangePointToCredit()
	{
		return Mage::getStoreConfig('rewardpoints/exchange_to_credit/enabled');
	}
	public function getCheckoutRewardPointsRule($quote)
	{
		$rules = array();

		$rewardOrder = Mage::getStoreConfig('rewardpoints/earning_points/points_to_purchasing_order');
		if($rewardOrder)
		{
			$point = explode('/',$rewardOrder);
			$_point = $point[0];
			if(sizeof($point)==2)
			{
				$rate = Mage::helper('core')->currency(1,false);
				// convert price from current currency to base currency
				$total = $quote->getGrandTotal();
				$_point = ($total / ($point[1]*$rate)) * $point[0];				
			}
			if($_point >0){
				$strPoiOrdr = '(Points / Order : '.$point[0] .'/'.$point[1].') ';
				$rules[] = array('message'=>Mage::helper('rewardpoints')->__('%s for %s grand total of this order',$this->formatPoints($_point).' '.$strPoiOrdr,$this->formatMoney($quote->getGrandTotal()/$rate)),'amount'=>$_point, 'qty'=>1);
			}
		}
		 
		foreach($quote->getAllItems() as $item)
		{
			$product = $item->getProduct()->load($item->getProduct()->getId());
			if($product->getData('reward_point_product'))
			{
				$strPrdPoints = $this->formatPointsWithoutName($product->getData('reward_point_product'));
				$strPrdPoints2 = $item->getQty()>1?$item->getQty()*$strPrdPoints.' '.$this->getPointCurency():$this->formatPoints($product->getData('reward_point_product'));
				$strPrdQtyPoints = '(Qty x Points : '.$item->getQty().' x '.$strPrdPoints.')';				
				$rules[] = array('message'=>$this->__('%s for product : %s',$strPrdPoints2 .' '.$strPrdQtyPoints ,$product->getName()),'amount'=>$product->getData('reward_point_product'),'qty'=>$item->getQty());
			}
		}
		return $rules;
	}

	public function roundPoints($points,$up = true)
	{
		$config = Mage::getStoreConfig('rewardpoints/config/point_money_rate');
		$rate = explode("/",$config);
		$tmp = (int)($points/$rate[0]) * $rate[0];
		if($up)
		return $tmp<$points?$tmp+$rate[0]:$tmp;
		return $tmp;
	}

	public function formatNumber($value)
	{
		return number_format($value,0,'.',',');
	}
	//for invitation
	public function enabled()
	{
		return Mage::getStoreConfig('invitation/config/enabled');
	}

	//get Invitation link of customer.
	public function getLink(Mage_Customer_Model_Customer $customer)
	{		
	/*$objInvColl = Mage::getModel('rewardpoints/invitations')->getCollection();
	$arrInvColl = $objInvColl->getReferralKey($this->getCurrentCustomer()->getId());
	foreach($arrInvColl as $arrInvCollVal){
		$strRefKey = $arrInvCollVal->referral_key;
	}	*/	
	 return  trim(Mage::getUrl('rewardpoints/index'),"/")."?c=".$customer->getEmail();
	}
	
	//get random referral key
	public function getRandomKey()
	{     
     return md5(uniqid(rand()));	 
	}

	public function rewardPointsEnabled()
	{
		return Mage::getStoreConfig('rewardpoints/config/enabled');
	}

	//expires date
	/*public function getExpDate($strTranDate)
	{
		$strExpDays = Mage::getStoreConfig('rewardpoints/general/reward_points_expire_days');
		$strTranDay = date('d',strtotime($strTranDate));
		$strTranMon = date('m',strtotime($strTranDate));
		$strTranYr = date('Y',strtotime($strTranDate));
		// calculate hours
		$strHours=$strExpDays * 24;
		$objValidInt = new Zend_Validate_Int();				
		if($objValidInt->isValid($strExpDays))
		// calculate the exp date
		return date('m/d/y',mktime($strHours, 0, 0, $strTranMon, $strTranDay, $strTranYr));
		return 0;
	}
	//calculate date difference
	function getDateDiff($strStartDate, $strEndDate) {
		$strFromDate = strtotime($strStartDate);
		$strToDate = strtotime($strEndDate);
		$strDateDiff = $strToDate - $strFromDate;
		return round($strDateDiff / 86400);
	}*/
}
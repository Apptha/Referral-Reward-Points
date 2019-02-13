<?php
class Apptha_Rewardpoints_Block_Rewardpoints_Info extends Mage_Core_Block_Template
{
	protected function _getCustomer()
	{
		return Mage::getModel('rewardpoints/customer')->load(Mage::getSingleton("customer/session")->getCustomer()->getId());
	}
	
	public function getRewardPoints()
	{
		return $this->_getCustomer()->getRewardPoint();
	}
	
	public function getPointPerMoney()
	{
		$config = Mage::getStoreConfig('rewardpoints/general/points_currency_rate');
		$rate = explode("/",$config);
		return $rate;
	}
	
	public function getPointPerCredit()
	{
		$config = Mage::getStoreConfig('rewardpoints/general/points_currency_rate');
		$rate = explode("/",$config);
		return $rate;
	}
	
	public function formatMoney($money)
	{
		return Mage::helper('rewardpoints')->formatMoney($money);
	}
	
	public function getMoney()
	{
		return $this->formatMoney(Mage::helper('rewardpoints')->exchangePointsToMoneys($this->getRewardPoints()));
	}
	
	public function canExchangeToCredit()
	{
		return Mage::helper('rewardpoints')->allowExchangePointToCredit() && Mage::helper('rewardpoints')->getCreditModule();
	}
	
	public function getPointCurency()
	{
		return Mage::helper('rewardpoints')->getPointCurency();
	}
}
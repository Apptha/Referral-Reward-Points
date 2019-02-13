<?php

class Apptha_Rewardpoints_Model_Newcustomer extends Mage_Customer_Model_Customer
{
	protected function _getRewardPointModel()
	{
		return Mage::getModel('rewardpoints/customer')->load($this->getId());
	}
	public function getRewardPoint()
	{
		$customer = $this->_getRewardPointModel();
		return $customer->getAppthaRewardPoint();
	}
	
	public function getAppthaRewardPoint()
	{
		return $this->getRewardPoint();
	}
	public function addRewardPoint($point)
	{
		$customer = $this->_getRewardPointModel();
		$customer->setAppthaRewardPoint($customer->getAppthaRewardPoint()+ $point);
		$customer->save();
	}
	
	public function getFriend()
	{
		$customer = $this->_getRewardPointModel();
		if($customer->getAppthaFriendId())
			return Mage::getModel('customer/customer')->load($customer->getAppthaFriendId());
		return false;
	}
	
	public function saveTransactionHistory($data = array())
	{
		$customer = $this->_getRewardPointModel();
		$customer->saveTransactionHistory($data);
	}
}
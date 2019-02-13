<?php

class Apptha_Rewardpoints_Model_Order extends Mage_Sales_Model_Order
{
	public function getRewardOrder()
	{
		return  Mage::getModel('rewardpoints/rewardpointsorder')->load($this->getId());
	}
	
	public function getRewardPoints()
	{
		return $this->getRewardOrder()->getRewardPoint();
	}
	
	public function getRewardMoneys()
	{
		return $this->getRewardOrder()->getMoney();
	}
	
	public function getRewardPointsMoneysRate()
	{
		return $this->getRewardOrder()->getRewardPointMoneyRate();
	}
	
	public function saveRewardOrder($orderData)
	{
		$collection = $this->getRewardOrder()->getCollection();
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = 'INSERT INTO '.$collection->getTable('rewardpointsorder').' VALUES('.$this->getId().','.$orderData['reward_point'].','. $orderData['money'].',\''. $orderData['reward_point_money_rate'].'\')';
        $write->query($sql);
	}
}
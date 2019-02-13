<?php

class Apptha_Rewardpoints_Model_Rewardpointsorder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/rewardpointsorder');
    }
	
	public function getRewardMoneys()
	{
		return $this->getMoney();
	}

	
	public function saveRewardOrder($orderData)
	{
		$collection = Mage::getModel('rewardpoints/rewardpointsorder')->getCollection();
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = 'INSERT INTO '.$collection->getTable('rewardpointsorder').' VALUES('.$orderData['order_id'].','.$orderData['reward_point'].','. $orderData['money'].',\''. $orderData['reward_point_money_rate'].'\')';
        $write->query($sql);
	}
}
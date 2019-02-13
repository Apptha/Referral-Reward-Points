<?php

class Apptha_Rewardpoints_Model_Mysql4_Invitations_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('rewardpoints/invitations');
	}
	//get referral key
	public function getReferralKey($strCusId){
		$this->getSelect()->where('customer_id='.intval($strCusId));
		return $this;
	}
	//check valid referral link
	public function getResult($strRefKey,$strCusId){			
		$this->getSelect() ->where('referral_key='."'$strRefKey'")
		->where('customer_id='.intval($strCusId))
		->where('status='.Apptha_Rewardpoints_Model_Status::COMPLETE);
		return $this;
	}
}
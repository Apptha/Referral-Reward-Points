<?php
class Apptha_Rewardpoints_Block_Rewardpoints extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    public function getTitle()
    {
    	return $this->__("Referral Reward Points Management");
    }
}
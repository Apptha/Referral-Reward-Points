<?php

class Apptha_Rewardpoints_Model_Rewardpoint extends Varien_Object
{
    const DISABLE_ALL						= 0;
	const TIER_REWARD_POINTS				= 1;
    const FIXED_REWARD_POINTS				= 2;
	

    static public function toOptionArray()
    {
        return array(
            self::TIER_REWARD_POINTS    => Mage::helper('rewardpoints')->__('Tier Reward Point'),
            self::FIXED_REWARD_POINTS   => Mage::helper('rewardpoints')->__('Fix Reward Point'),
            self::DISABLE_ALL		    => Mage::helper('rewardpoints')->__('Disable All')
        );
    }
}
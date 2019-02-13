<?php

class Apptha_Rewardpoints_Model_Position extends Varien_Object
{
    const BEFORE				= 1;
    const AFTER					= 2;
	

    static public function toOptionArray()
    {
        return array(
            self::BEFORE    				=> Mage::helper('rewardpoints')->__('Before Point Value'),
            self::AFTER		 			 	=> Mage::helper('rewardpoints')->__('After Point Value'),
        );
    }
}
<?php

class Apptha_Rewardpoints_Model_Action extends Varien_Object
{
    const ADDITION				= 1;
    const SUBTRACTION			= -1;
	

    static public function getOptionArray()
    {
        return array(
        	self::SUBTRACTION  			 	=> Mage::helper('rewardpoints')->__('Subtraction'),
            self::ADDITION    				=> Mage::helper('rewardpoints')->__('Addition'),
        );
    }
}
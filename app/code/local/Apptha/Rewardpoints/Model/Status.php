<?php

class Apptha_Rewardpoints_Model_Status extends Varien_Object
{
    const PENDING				= 1;		//haven't change points yet
    const COMPLETE				= 2;
    const UNCOMPLETE			= 0;
	

    static public function getOptionArray()
    {
        return array(
            self::PENDING    				=> Mage::helper('rewardpoints')->__('Pending'),
            self::COMPLETE  			 	=> Mage::helper('rewardpoints')->__('Complete'),
            self::UNCOMPLETE	    		=> Mage::helper('rewardpoints')->__('Uncomplete'),
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}
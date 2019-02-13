<?php

class Apptha_Rewardpoints_Model_Total extends Varien_Object
{
    private $_can_display = true;
    
	public function canDisplay()
    {
    	return $this->_can_display;
    }
    
    public function getTotalsForDisplay()
    {
    	return $this->getData();
    }
}
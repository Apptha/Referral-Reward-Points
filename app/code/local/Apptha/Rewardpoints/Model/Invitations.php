<?php

class Apptha_Rewardpoints_Model_Invitations extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    	
        parent::_construct();
        $this->_init('rewardpoints/invitations');
    }
}
<?php

class Apptha_Rewardpoints_Model_Mysql4_Rewardpointshistory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/rewardpointshistory');
    }
}
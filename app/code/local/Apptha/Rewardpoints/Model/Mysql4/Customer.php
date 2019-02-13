<?php

class Apptha_Rewardpoints_Model_Mysql4_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the rewardpoints_id refers to the key field in your database table.
        $this->_init('rewardpoints/customer', 'customer_id');
    }
}
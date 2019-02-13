<?php

$installer = $this;
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();
$sql ="ALTER TABLE {$collection->getTable('customer')} 
CHANGE `customer_id` `customer_id` INT( 11 ) UNSIGNED NOT NULL ,
DROP PRIMARY KEY";
$installer->run($sql);
$installer->endSetup();
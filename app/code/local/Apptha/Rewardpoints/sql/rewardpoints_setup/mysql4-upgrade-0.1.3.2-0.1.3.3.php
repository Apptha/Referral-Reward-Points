<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;
$collection = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection();
$installer->startSetup();
$sql ="
ALTER TABLE {$collection->getTable('rewardpointshistory')} ADD `balance` INT NOT NULL AFTER `amount`;
";
$installer->run($sql);
$installer->endSetup();
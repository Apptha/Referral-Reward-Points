<?php

$installer = $this;
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();
$sql ="
ALTER TABLE {$collection->getTable('rewardpointshistory')} ADD `status` INT NOT NULL;
";
$installer->run($sql);
$installer->endSetup();
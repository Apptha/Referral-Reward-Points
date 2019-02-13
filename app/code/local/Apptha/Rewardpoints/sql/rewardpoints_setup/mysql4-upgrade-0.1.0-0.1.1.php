<?php

$installer = $this;
$customers = Mage::getModel('customer/customer')->getCollection();
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();
$sql ="";
foreach($customers as $customer)
{
	$sql .="INSERT INTO {$collection->getTable('customer')} VALUES(".$customer->getId().",0,0);";
}
$installer->run($sql);
$installer->endSetup();
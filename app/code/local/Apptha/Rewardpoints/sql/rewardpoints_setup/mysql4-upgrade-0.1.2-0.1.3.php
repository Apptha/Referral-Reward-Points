<?php

$installer = $this;
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();
$sql ="
-- DROP TABLE IF EXISTS {$collection->getTable('rewardpointsorder')};
CREATE TABLE {$collection->getTable('rewardpointsorder')} (
  `order_id` int(11) unsigned NOT NULL,
  `reward_point` int(11) unsigned NOT NULL,
  `money` float(11) unsigned NOT NULL,
  `reward_point_money_rate` varchar(255) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$collection->getTable('customer')} ADD PRIMARY KEY (customer_id);
";
$installer->run($sql);
$installer->endSetup();
<?php

$installer = $this;
$collection = Mage::getModel('rewardpoints/customer')->getCollection();
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$collection->getTable('rewardpointshistory')};
CREATE TABLE {$collection->getTable('rewardpointshistory')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `type_of_transaction` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  `balance` INT(11) NOT NULL,
  `transaction_detail` varchar(255) NOT NULL default '',
  `transaction_time` datetime NULL,
  `status` INT NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('customer')};
CREATE TABLE {$collection->getTable('customer')} (
  `customer_id` INT( 11 ) unsigned NOT NULL,
  `apptha_reward_point` int(11) unsigned NOT NULL,
  `apptha_friend_id` int(11) unsigned NOT NULL,
  `last_checkout` DATETIME NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$collection->getTable('rewardpointsorder')};
CREATE TABLE {$collection->getTable('rewardpointsorder')} (
  `order_id` int(11) unsigned NOT NULL,
  `reward_point` int(11) unsigned NOT NULL,
  `money` float(11) unsigned NOT NULL,
  `reward_point_money_rate` varchar(255) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
//create reward_point_product attribute under catalog->product->General->Reward Points
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('catalog_product','reward_point_product');
$setup->addAttribute('catalog_product', 'reward_point_product', array(
	'label' => 'Reward Points',
	'type' => 'int',
	'input' => 'text',
	'visible' => true,
	'required' => false,
	'position' => 10,
));
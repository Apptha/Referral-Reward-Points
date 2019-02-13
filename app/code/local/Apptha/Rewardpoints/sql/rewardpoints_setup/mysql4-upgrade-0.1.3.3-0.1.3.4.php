<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('catalog_product','reward_point_product');
$setup->addAttribute('catalog_product', 'reward_point_product', array(
	'label' => 'Set As Reward Points Product',
	'type' => 'int',
	'input' => 'select',
	'source' => 'eav/entity_attribute_source_boolean',
	'visible' => true,
	'required' => false,
	'position' => 10,
));

$setup->addAttribute('catalog_product', 'reward_point_will_get', array(
	'label' => 'Reward Points Will Get',
	'type' => 'int',
	'input' => 'text',
	'visible' => true,
	'required' => false,
	'position' => 11,
));
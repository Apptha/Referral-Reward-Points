<?php

class Apptha_Rewardpoints_Model_Type extends Varien_Object
{
    const REGISTERING				= 1;
    const SUBMIT_PRODUCT_REVIEW		= 2;
	const PURCHASE_PRODUCT			= 3;
	const INVITE_FRIEND				= 4;
	const FRIEND_REGISTERING		= 5;
	const FRIEND_FIRST_PURCHASE		= 6;
	const RECIVE_FROM_FRIEND		= 7;
	const CHECKOUT_ORDER			= 8;
	const SEND_TO_FRIEND			= 9;	
	const USE_TO_CHECKOUT			= 11;
	const ADMIN_ADDITION			= 12;	
	const FRIEND_NEXT_PURCHASE		= 14;
	const SUBMIT_POLL				= 15;
	const SIGNING_UP_NEWLETTER		= 16;
	const ADMIN_SUBTRACT			= 17;
	const BUY_POINTS				= 18;
	const SUBMIT_PRODUCT_TAG		= 19;
	

    static public function getOptionArray()
    {
        return array(
            self::REGISTERING    			=> Mage::helper('rewardpoints')->__('Register'),
            self::SUBMIT_PRODUCT_REVIEW   	=> Mage::helper('rewardpoints')->__('Submit Product Review'),
            self::PURCHASE_PRODUCT    		=> Mage::helper('rewardpoints')->__('Purchase Product'),
            self::INVITE_FRIEND   			=> Mage::helper('rewardpoints')->__('Invite A Friend'),
            self::FRIEND_REGISTERING    	=> Mage::helper('rewardpoints')->__('Friend Registering'),
            self::FRIEND_FIRST_PURCHASE		=> Mage::helper('rewardpoints')->__('Friend First Purchase'),
            self::RECIVE_FROM_FRIEND    	=> Mage::helper('rewardpoints')->__('Receive From Friend'),
            self::SEND_TO_FRIEND   			=> Mage::helper('rewardpoints')->__('Share Points To Friend'),
            self::CHECKOUT_ORDER    		=> Mage::helper('rewardpoints')->__('Checkout An Order'),            
            self::USE_TO_CHECKOUT			=> Mage::helper('rewardpoints')->__('Use To Checkout'),
            self::ADMIN_ADDITION			=> Mage::helper('rewardpoints')->__('Add By Admin'),
            self::ADMIN_SUBTRACT			=> Mage::helper('rewardpoints')->__('Subtract By Admin'),           
            self::FRIEND_NEXT_PURCHASE		=> Mage::helper('rewardpoints')->__('Friend Second Purchase'),
            self::SUBMIT_POLL				=> Mage::helper('rewardpoints')->__('Submit Poll'),
            self::SIGNING_UP_NEWLETTER		=> Mage::helper('rewardpoints')->__('Sign Up For Newsletter'),            
            self::SUBMIT_PRODUCT_TAG		=> Mage::helper('rewardpoints')->__('Submit Product Tag'),
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
    static public function getTransactionDetail($type, $detail = null, $status=null,$is_admin= false)
    {
    	$result = "";
    	switch($type)
    	{
    		case self::REGISTERING:
    			$result = Mage::helper('rewardpoints')->__("Earned for registering");
    			break;
    		case self::SUBMIT_PRODUCT_REVIEW:
				$detail = explode('|',$detail);
    			$review = Mage::getModel('review/review')->load($detail[0]);
				$object = Mage::getModel('catalog/product');
				
				if($review->getId()){
					$object->load($review->getEntityPkValue());
				}else{
					$object->load($detail[1]);
				}
				
				$url = $object->getProductUrl();
    			if($is_admin) $url = Mage::getUrl('adminhtml/catalog_product/edit',array('id'=>$object->getId()));
				$result = Mage::helper('rewardpoints')->__("Earned for reviewing product <b><a href='%s'>%s</a></b>",$url, $object->getName());
				
    			break;
    		case self::PURCHASE_PRODUCT:
    			$_detail = explode('|',$detail);
    			$product_id = $_detail[0];
    			$object = Mage::getModel('catalog/product')->load($product_id);
    			$url = $object->getProductUrl();
    			if($is_admin) $url = Mage::getUrl('adminhtml/catalog_product/edit',array('id'=>$product_id));
    			$result = Mage::helper('rewardpoints')->__("Earned for purchasing product <b><a href='%s'>%s</a></b>",$url, $object->getName());
    			break;
    		case self::INVITE_FRIEND:
    			$result = Mage::helper('rewardpoints')->__("Earned for friend (<b>%s</b>) visit refferal link",$detail);
    			break;	
    		case self::FRIEND_REGISTERING:
    			$object = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('rewardpoints')->__("Earned for friend (<b>%s</b>) registering",$object->getEmail());
    			break;
    		case self::FRIEND_FIRST_PURCHASE:
    			$detail = explode('|',$detail);
    			$object = Mage::getModel('customer/customer')->load($detail[0]);
    			$result = Mage::helper('rewardpoints')->__("Earned for friend first purchase (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::FRIEND_NEXT_PURCHASE:
    			$detail = explode('|',$detail);
    			$object = Mage::getModel('customer/customer')->load($detail[0]);
    			$result = Mage::helper('rewardpoints')->__("Earned for friend second purchase (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::RECIVE_FROM_FRIEND:
    			$object = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('rewardpoints')->__("Earned points from friend (<b>%s</b>)",$object->getEmail());
    			break;
    		case self::SEND_TO_FRIEND:
    			$email = $detail;
    			if($status == Apptha_Rewardpoints_Model_Status::COMPLETE){
    				$object = Mage::getModel('customer/customer')->load($detail);
    				$email = $object->getEmail();
    			}
    			
    			$result = Mage::helper('rewardpoints')->__("Share points to friend (<b>%s</b>)",$email);
    			break;
    		case self::CHECKOUT_ORDER:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("Earned for purchasing order <b><a href='%s'>#%s</a></b>",$url,$detail);
    			break;
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('rewardpoints')->__("Use to purchase order <b><a href='%s'>#%s</a></b>",$url,$detail);
    			break;
    		case self::ADMIN_ADDITION:
    			$detail = explode('|',$detail);
    			$result = Mage::helper('rewardpoints')->__("Reward from admin: <i>%s</i>",$detail[0]);
    			break;
    		case self::ADMIN_SUBTRACT:
    			$detail = explode('|',$detail);
    			$result = Mage::helper('rewardpoints')->__("Subtract by admin: <i>%s</i>",$detail[0]);
    			break;
    		case self::SUBMIT_POLL:
    			$result = Mage::helper('rewardpoints')->__("Earned for participating in poll");
    			break;
    		case self::SIGNING_UP_NEWLETTER:
    			$result = Mage::helper('rewardpoints')->__("Earned for signing up newsletter");
    			break;    
    	    case self::SUBMIT_PRODUCT_TAG:
    			$result = Mage::helper('rewardpoints')->__("Earned for submitting tags");
    			break;		
    	}
    	if($is_admin)
    	{
    		$result = str_replace('You','Customer',$result);
    		$result = str_replace('Your','Customer\'s',$result);
    	}
    	return $result;
    }
    
    static public function getAmountWithSign($amount, $type)
    {
    	$result = $amount;
    	switch ($type)
    	{
    		case self::REGISTERING:
    		case self::SUBMIT_PRODUCT_REVIEW:
    		case self::SUBMIT_PRODUCT_TAG:
    		case self::PURCHASE_PRODUCT:
    		case self::INVITE_FRIEND:
    		case self::FRIEND_REGISTERING:
    		case self::FRIEND_FIRST_PURCHASE:
    		case self::FRIEND_NEXT_PURCHASE:
    		case self::RECIVE_FROM_FRIEND:
    		case self::CHECKOUT_ORDER:
    		case self::SUBMIT_POLL:
    		case self::SIGNING_UP_NEWLETTER:
    		case self::ADMIN_ADDITION:
    		case self::BUY_POINTS:
				$result = "+".$amount;
				break;
    		case self::SEND_TO_FRIEND:
    		case self::USE_TO_CHECKOUT:
    		case self::ADMIN_SUBTRACT:
    			$result = -$amount;
    		break;
    	}
    	return $result;
    }
}
<?php

class Apptha_Rewardpoints_Model_Invitation extends Mage_Core_Model_Abstract
{
	//click on referral link get points
    public function referralLinkClick($argv)
    {
    	$invite = $argv->getInvite();    	
    	$referral_by = $argv->getReferralBy();
    	$request = $argv->getRequest();
    	$customer = Mage::getModel('customer/customer');    	
    	
    	switch ($referral_by){
    		case "1":    			
    			$customer->load($invite);
    			break;
    		case "2":    			
    			$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($invite);
    			break;
    	}		
		
		if(Mage::helper('rewardpoints')->isRefLinkEnabled())
		{
			$objValEmail = new Zend_Validate_EmailAddress();
			if ($objValEmail->isValid($invite)) {			
			$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($invite);					
			if(method_exists($request,'getClientIp'))
				$clientIP = $request->getClientIp(true);
			else
			$clientIP = $request->getServer('REMOTE_ADDR');
			//check already referred from this ip address
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('transaction_detail',$clientIP)
			->addFieldToFilter('customer_id',$customer->getId());				
			if(!sizeof($transactions))
			{				
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
				$points = Mage::getStoreConfig('rewardpoints/earning_points/referral_link');
				$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::INVITE_FRIEND, 'amount'=>$points, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$clientIP, 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);						
	            $_customer->saveTransactionHistory($historyData);
				$_customer->addRewardPoint($points);
			}
		}
		}
			//customer id set to session
			Mage::getSingleton('customer/session')->setFriend($customer->getId());			
    }
}
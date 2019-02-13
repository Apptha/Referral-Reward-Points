<?php

class Apptha_Rewardpoints_Model_Credit extends Varien_Object
{
	/**
	 * Exchange Credit to reward points
	 */
	public function exchange($argv)
	{
		$result = false;
		$customer 		= $argv->getCustomer();		// Instance of Mage_Core_Model_Customer
		$rewardPoints 	= $argv->getCredit();		// (int) The Reward 
		
		try{
			if($rewardPoints < 0) throw('reward points is incorrect');
			$_customer 		= Mage::getModel('rewardpoints/customer')->load($customer->getId());
			
			$_customer->addRewardPoint($rewardPoints);
			 $historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::EXCHANGE_FROM_CREDIT, 'amount'=>$rewardPoints, 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
            $_customer->saveTransactionHistory($historyData);
			$result = true;
			
		}catch(Mage_Core_Exception $e)
		{
			$result = false;
		}
		
		Mage::dispatchEvent('apptha_rewardpoints_exchange_credit_to_rewardpoints_after', array(
			'customer'	=> $customer,
            'result'    => $result,
        ));
	}
}
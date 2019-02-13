<?php

class Apptha_Rewardpoints_Model_Admin_Customer extends Mage_Core_Model_Abstract
{
    public function saveRewardPoints($argv)
    {    	
    	$customer 	= $argv->getCustomer();
    	$request 	= $argv->getRequest();
    	$_customer 	= Mage::getModel('rewardpoints/customer')->load($customer->getId());
    	$oldPoints 	= $_customer->getAppthaRewardPoint();
    	$amount 	= $request->getParam('reward_points_amount');
    	$action		= $request->getParam('reward_points_action');
    	$comment	= $request->getParam('reward_points_comment');
    	$newPoints 	= $oldPoints + $amount * $action;
    	
    	if($newPoints < 0) $newPoints = 0;
    	$amount = abs($newPoints - $oldPoints);
    	
    	if($amount > 0){
	    	$detail =$comment;
	    	$balance = $_customer->getAppthaRewardPoint()-(($action<0)?$amount:0);
	    	$historyData = array('type_of_transaction'=>($action>0)?Apptha_Rewardpoints_Model_Type::ADMIN_ADDITION:Apptha_Rewardpoints_Model_Type::ADMIN_SUBTRACT, 'amount'=>$amount, 'balance'=>$balance, 'transaction_detail'=>$detail, 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
			$_customer->saveTransactionHistory($historyData);			
			$_customer->setData('apptha_reward_point',$newPoints);
	    	$_customer->save();
    	}
    }
}
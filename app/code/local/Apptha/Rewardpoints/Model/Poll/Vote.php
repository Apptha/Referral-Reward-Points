<?php
class Apptha_Rewardpoints_Model_Poll_Vote extends Mage_Poll_Model_Poll_Vote
{
    protected $_eventPrefix = 'poll_vote';
    protected $_eventObject = 'vote';
    
	protected function _construct()
    {
        return parent::_construct();
    }
    
    public function voteAfterSave($argv)
    {
    	if(Mage::helper('rewardpoints')->isPollEnabled()){
    	$vote = $argv->getVote();
    	if($vote->getCustomerId())
    	{
    		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('type_of_transaction',Apptha_Rewardpoints_Model_Type::SUBMIT_POLL)
			->addFieldToFilter('customer_id',$vote->getCustomerId());	
			$points = Mage::getStoreConfig('rewardpoints/earning_points/participating_in_poll');
			//for poll limitations
			if(Mage::helper('rewardpoints')->isPollLimEnabled())
			{
				if(count($transactions) <= Mage::helper('rewardpoints')->isPollLimEnabled()){    		
    		if($points)
    		{    			
    			$_customer = Mage::getModel("rewardpoints/customer")->load($vote->getCustomerId());
    			
    			$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SUBMIT_POLL, 'amount'=>(int)$points, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$vote->getPollId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
	            $_customer->saveTransactionHistory($historyData);
	            $_customer->addRewardPoint($points);
	            Mage::getSingleton('core/session')->addSuccess(Mage::helper("rewardpoints")->__("You have been rewarded %s %s for participating in poll",$points,Mage::helper('rewardpoints')->getPointCurency()));
    		}
				}
			}else{
				//no limitations
			    		if($points)
    		{    			
    			$_customer = Mage::getModel("rewardpoints/customer")->load($vote->getCustomerId());
    			
    			$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SUBMIT_POLL, 'amount'=>(int)$points, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$vote->getPollId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
	            $_customer->saveTransactionHistory($historyData);
	            $_customer->addRewardPoint($points);
	            Mage::getSingleton('core/session')->addSuccess(Mage::helper("rewardpoints")->__("You have been rewarded %s %s for participating in poll",$points,Mage::helper('rewardpoints')->getPointCurency()));
    		}
			}
    	}
    }
    }
}

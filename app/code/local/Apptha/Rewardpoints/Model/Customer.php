<?php

class Apptha_Rewardpoints_Model_Customer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/customer');
    }
	
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * 
     * @param $data=array('type_of_transaction'=>y, 'amount'=>z, 'transaction_detail'=>a, 'transaction_time'=>b)
     */
	
    public function saveTransactionHistory($data = array())
    {    	
    	$data['customer_id'] = $this->getId();
    	$history = Mage::getModel('rewardpoints/rewardpointshistory');
    	$history->setData($data);
    	$history->save();
    }
    
	public function getRewardPoint()
	{
		return $this->getAppthaRewardPoint();
	}
	
	public function addRewardPoint($point)
	{
		$this->setAppthaRewardPoint($this->getAppthaRewardPoint()+ $point);
		$this->save();
	}
	
	public function getFriend()
	{
		if($this->getAppthaFriendId())
			return Mage::getModel('rewardpoints/customer')->load($this->getAppthaFriendId());
		return false;
	}
	
	public function getCustomerModel()
	{
		return Mage::getModel('customer/customer')->load($this->getId());
	}
	
	public function customerSaveAfter($param)
	{		
		//logged customer id
		$customer = $param->getCustomer();
		$strSubscribe = $customer->getIsSubscribed();
		$customer->getId();
		//check customer id available in customer table
		$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
		
		if(Mage::helper('rewardpoints')->moduleEnabled() && !($_customer->getId()))
		{
			if(Mage::helper('rewardpoints')->isRegEnabled()){			
            //check fried id in session (from click referral link)
        	$friend_id = $this->_getSession()->getFriend();
            if($friend_id && Mage::helper('rewardpoints')->isInviteEnabled() && Mage::helper('rewardpoints')->isFrndRegEnabled())
            {            	
	            $friend = Mage::getModel('rewardpoints/customer')->load($friend_id);
	            $point = Mage::getStoreConfig('rewardpoints/earning_points/friend_registration');
	            if($friend->getId())
	            {
	            	//add reward point for friend registration to already exiting customer
		            $historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::FRIEND_REGISTERING, 'amount'=>$point, 'balance'=>$friend->getAppthaRewardPoint(), 'transaction_detail'=>$customer->getId(), 'transaction_time'=>now(),'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);		            
		            $friend->saveTransactionHistory($historyData);
		            $friend->setAppthaRewardPoint($friend->getAppthaRewardPoint() + $point);
		            $friend->save();
		            //$this->_getSession()->unsetData('friend');
	            }
            }
			
			//init reward points of customer
			$_customer = Mage::getModel('rewardpoints/customer')->getCollection();
            $point = Mage::getStoreConfig('rewardpoints/earning_points/registration');
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = 'INSERT INTO '.$_customer->getTable('customer').'(customer_id,apptha_reward_point,apptha_friend_id) VALUES('.$customer->getId().',0,'. (($friend_id && Mage::helper('rewardpoints')->isInviteEnabled())?$friend_id:0).')';                                
            $write->query($sql);
            
			//Save history transaction
			$_customerModel = Mage::getModel('rewardpoints/customer')->load($customer->getId());
            $historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::REGISTERING, 'amount'=>$point, 'balance'=>$_customerModel->getAppthaRewardPoint(), 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);            
            $_customerModel->saveTransactionHistory($historyData);
			$_customerModel->setAppthaRewardPoint($point);
			$_customerModel->save();
			//point for newsletter subscribtion during registration
			if($strSubscribe && Mage::helper('rewardpoints')->isSubNewLtrEnabled()){
			$strSubNewLtrPoint = Mage::helper('rewardpoints')->isSubNewLtrEnabled();
			$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SIGNING_UP_NEWLETTER, 'amount'=>$strSubNewLtrPoint, 'balance'=>$_customerModel->getAppthaRewardPoint(), 'transaction_detail'=>'', 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);            
            $_customerModel->saveTransactionHistory($historyData);
			$_customerModel->setAppthaRewardPoint($_customerModel->getAppthaRewardPoint() + $strSubNewLtrPoint);
			$_customerModel->save();
			Mage::getSingleton('customer/session')->addSuccess(Mage::helper('rewardpoints')->__('You earned %s %s points for registration.',$point, Mage::helper('rewardpoints')->getPointCurency()));
			Mage::getSingleton('customer/session')->addSuccess(Mage::helper('rewardpoints')->__('You earned %s %s points for sign up newsletter.',$strSubNewLtrPoint, Mage::helper('rewardpoints')->getPointCurency()));
			}
			if(!Mage::helper('rewardpoints')->isSubNewLtrEnabled() || !$strSubscribe){
			Mage::getSingleton('customer/session')->addSuccess(Mage::helper('rewardpoints')->__('You earned %s %s points for registration.',$point, Mage::helper('rewardpoints')->getPointCurency()));
			}
			Mage::dispatchEvent('customer_account_registed_rewardpoint');
		}
		}
	}	
}
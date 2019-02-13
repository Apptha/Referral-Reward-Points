<?php
class Apptha_Rewardpoints_Model_Transaction extends Varien_Object
{
	//when customer logged into my rewardpoints
	public function update($argv)
	{		
		$customer = Mage::getModel('rewardpoints/customer')->load($argv->getModel()->getId());
		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customer->getId())
					->addFieldToFilter('status',Apptha_Rewardpoints_Model_Status::PENDING)
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC')
		;
		
		//expires date			
		/*if(Mage::helper('rewardpoints')->isExpDaysEnabled()){			
		$arrTrans = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customer->getId())					
					->addOrder('transaction_time','DESC')
					->addOrder('history_id','DESC')
		;		
		if(sizeof($arrTrans))foreach ($arrTrans as $arrTransVal){			
			$strTransPoints = $arrTransVal->getAmount();
			$strTransDate = $arrTransVal->getTransactionTime();
			$strExpDate = Mage::helper('rewardpoints')->getExpDate($strTransDate);
			$strStartDate = date('Y-m-d',strtotime(now()));
			$strEndDate = date('Y-m-d',strtotime($strExpDate));
			$strRes = (int)Mage::helper('rewardpoints')->getDateDiff($strStartDate,$strEndDate);			
			if($strRes == '0'){				
			$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
			$_customer->addRewardPoint(-$strTransPoints);						
			}			
		}
		}*/
		//because select by current customer so have no record
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//updation for review product
				case Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_REVIEW:
					$reviewId = $transaction->getTransactionDetail();
					$review = Mage::getModel('review/review')->load($reviewId);
					$status = $transaction->getStatus();
					if($review->getId())
					{
						if($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED)
						{
							//echo 'complete';die;
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
							$customer->addRewardPoint($transaction->getAmount());
						}else if($review->getStatusId() == Mage_Review_Model_Review::STATUS_NOT_APPROVED)
						{
							$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
						}
					}else{
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					}
					//echo 'status';die;
					$transaction->setStatus($status)->save();
					break;
				//updation for tag product
				case Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_TAG:
					$tagId = $transaction->getTransactionDetail();					
					$tag = Mage::getModel('tag/tag')->load($tagId);					
					$status = $transaction->getStatus();					
					if($tag->getFirstCustomerId())
					{
						if($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_APPROVED)
						{							
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
							$customer->addRewardPoint($transaction->getAmount());
						}else if($tag->getStatus() == Mage_Review_Model_Review::STATUS_NOT_APPROVED)
						{							
							$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
						}
					}else{						
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					}					
					$transaction->setStatus($status)->save();
					break;
					
				//updation for purchase product
				case Apptha_Rewardpoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					$order = Mage::getModel('sales/order')->load($detail[1]);
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
							$customer->addRewardPoint($transaction->getAmount());
						}
					}else {
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					}
					
					$transaction->setStatus($status)->save();
					break;
					
					
				case Apptha_Rewardpoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case Apptha_Rewardpoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					$order = Mage::getModel('sales/order')->load($detail[1]);
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
							$customer->addRewardPoint($transaction->getAmount());
						}
					}else {
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					$transaction->setStatus($status)->save();
					break;
					
					
				/*case Apptha_Rewardpoints_Model_Type::SEND_TO_FRIEND:
					//if the time is expired add reward points back to customer
					$oldtime =strtotime($transaction->getTransactionTime());
					$currentTime = strtotime(now());
					$hour = ($currentTime - $oldtime)/(60*60);
					$hourConfig = Mage::getStoreConfig('rewardpoints/send_reward_points/time_life');
					if($hourConfig && ($hour > $hourConfig))
					{
						$customer->addRewardPoint($transaction->getAmount());
						$transaction->setStatus(Apptha_Rewardpoints_Model_Status::UNCOMPLETE);
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
						$transaction->save();
					}
					break;*/
					
				case Apptha_Rewardpoints_Model_Type::USE_TO_CHECKOUT:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setTransactionTime(now());
						}
					}else {
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$customer->addRewardPoint($transaction->getAmount());
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					$transaction->setStatus($status)->save();
					break;
			
			
				case Apptha_Rewardpoints_Model_Type::CHECKOUT_ORDER:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					$status = $transaction->getStatus();
					if($order && $order->getStatus() != Mage_Sales_Model_Order::STATE_CANCELED)
					{
						if($order->hasInvoices())
						{
							$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
							$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
							$customer->addRewardPoint($transaction->getAmount());
						}
					}else {
						$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
						$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					}
					
					$transaction->setStatus($status)->save();
					break;
			}
		}
		
				
		$_transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('transaction_detail',$customer->getCustomerModel()->getEmail())
					->addFieldToFilter('type_of_transaction',Apptha_Rewardpoints_Model_Type::SEND_TO_FRIEND)
					->addFieldToFilter('status',Apptha_Rewardpoints_Model_Status::PENDING)
		;

		if(sizeof($_transactions)) foreach($_transactions as $_transaction)
		{			
			$customer->addRewardPoint($_transaction->getAmount());
			$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::RECIVE_FROM_FRIEND, 'amount'=>$_transaction->getAmount(), 'transaction_detail'=>$_transaction->getCustomerId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
		    $customer->saveTransactionHistory($historyData);
		    $_transaction->setStatus(Apptha_Rewardpoints_Model_Status::COMPLETE)->setTransactionDetail($customer->getCustomerId())->save();
		}	
		//for referral link
		/*if(Mage::helper('rewardpoints')->isRefLinkEnabled())
		{
			$strRefKey = Mage::helper('rewardpoints')->getRandomKey();
			$transactions = Mage::getModel('rewardpoints/invitations')->getCollection()
			->addFieldToFilter('referral_key',$strRefKey)
			->addFieldToFilter('customer_id',$customer->getId())
			->addFieldToFilter('status',Apptha_Rewardpoints_Model_Status::PENDING);			
			if(!sizeof($transactions)){
			$_customer = Mage::getModel('rewardpoints/invitations')->getCollection();
            $point = Mage::getStoreConfig('rewardpoints/earning_points/registration');
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $strStatus = Apptha_Rewardpoints_Model_Status::COMPLETE;
            $strLimit = 0;
            $sql = 'INSERT INTO '.$_customer->getTable('invitations').'(`customer_id`,`referral_key`,`limit`,`date`,`status`) VALUES('.$customer->getId().',"'.$strRefKey.'",'.$strLimit.',"'.now().'",'.$strStatus.')';                     
            $write->query($sql);
			}
		}*/	
	}
}
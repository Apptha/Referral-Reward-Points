<?php
class Apptha_Rewardpoints_Model_Admin_Review_Product
{
	public function save($argv)
	{				
			$review = $argv->getObject();
			if(Mage::helper('rewardpoints')->isRevEnabled())
			{ 
					$transactions = Mage::getResourceModel('rewardpoints/rewardpointshistory_collection')
					->addFieldToFilter('type_of_transaction',Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_REVIEW)
					->addFieldToFilter('transaction_detail',$review->getId()."|".$review->getEntityPkValue());
					if(!sizeof($transactions))
					{						
	                   	$_customer = Mage::getModel('rewardpoints/customer')->load($review->getData('customer_id'));	                   	
	                    $points = Mage::getStoreConfig('rewardpoints/earning_points/reviewing_product');
	                    
	                    $status = Apptha_Rewardpoints_Model_Status::PENDING;
	                    
	                    $historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_REVIEW, 'amount'=>$points, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$review->getId()."|".$review->getEntityPkValue(), 'transaction_time'=>now(),'status'=>$status);	                    
	                    $_customer->saveTransactionHistory($historyData);	
	                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('You will earn %s %s for reviewing this product after admin approval.',$points, Mage::helper('rewardpoints')->getPointCurency()));
						if($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED)
	                    {	                    	
	                    	$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
	                    	$_customer->addRewardPoint($points);
	                    }
					}
			}
	}
}
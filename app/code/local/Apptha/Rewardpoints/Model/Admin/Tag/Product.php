<?php
class Apptha_Rewardpoints_Model_Admin_Tag_Product
{
	public function save($argv)
	{				
			$tag = $argv->getObject();
			if(Mage::helper('rewardpoints')->isTagEnabled())
			{ 
					$transactions = Mage::getResourceModel('rewardpoints/rewardpointshistory_collection')
					->addFieldToFilter('type_of_transaction',Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_TAG)
					->addFieldToFilter('transaction_detail',$tag->getTagId());
					if(!sizeof($transactions))
					{						
	                   	$_customer = Mage::getModel('rewardpoints/customer')->load($tag->getFirstCustomerId());	                   	
	                    $points = Mage::getStoreConfig('rewardpoints/earning_points/tagging_product');	                    
	                    $status = Apptha_Rewardpoints_Model_Status::PENDING;	                    
	                    $historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SUBMIT_PRODUCT_TAG, 'amount'=>$points, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$tag->getTagId(), 'transaction_time'=>now(),'status'=>$status);	                    
	                    $_customer->saveTransactionHistory($historyData);	  
	                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('You will earn %s %s for tag this product after admin approval.',$points, Mage::helper('rewardpoints')->getPointCurency()));                  
						if($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_APPROVED)						
	                    {	                    		                    	
	                    	$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
	                    	$_customer->addRewardPoint($points);
	                    }
					}
			}
	}
}
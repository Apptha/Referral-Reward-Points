<?php

class Apptha_Rewardpoints_Model_Checkout extends Mage_Core_Model_Abstract
{
	public function placeAfter($argv)
	{
		if(Mage::helper('rewardpoints')->moduleEnabled())
		{			
			$order = $argv->getOrder();
			$customer = Mage::getSingleton('customer/session')->getCustomer();			
			$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());			
			if($customer->getId()) $customer=Mage::getModel('customer/customer')->load($order->getCustomerId());

			if($customer->getId()){
				 
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
				//Subtract reward points of customer and save reward points to order if customer use this point to checkout
				$rewardpoints = Mage::getSingleton('checkout/session')->getRewardPoints();
				if($rewardpoints)
				{
					//Subtract reward points of customer
					$_customer->addRewardPoint(-$rewardpoints);
					$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::USE_TO_CHECKOUT, 'amount'=>(int)$rewardpoints, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);
					$_customer->saveTransactionHistory($historyData);

					//Save reward point to order
					$orderData = array('order_id'=>$order->getId(),'reward_point'=>$rewardpoints, 'money'=>Mage::getSingleton('checkout/session')->getDiscount(),'reward_point_money_rate'=>Mage::helper('rewardpoints')->getPointMoneyRateConfig());
					$_order = Mage::getModel('rewardpoints/rewardpointsorder');
					$_order->saveRewardOrder($orderData);
				}
				 
				//reward points to customer with order
				if(Mage::helper('rewardpoints')->isOrdRewdPointEnabled())
				{ 
					$rewardOrder = Mage::helper('rewardpoints')->isOrdRewdPointEnabled();
					$point = explode('/',$rewardOrder);
					if(sizeof($point)==2)
					{
						$total = $order->getGrandTotal();
						$_point = ($total / $point[1]) * $point[0];						
					}
					if($_point > 0){					
						$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::CHECKOUT_ORDER, 'amount'=>$_point,'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$order->getIncrementId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);						
						$_customer->saveTransactionHistory($historyData);
					}
				}
				 
				 
				//reward points to customer with specific product				
				if(Mage::helper('rewardpoints')->isPrdRewdPointEnabled()){
					$quote = Mage::getSingleton('checkout/session')->getQuote();
					foreach($quote->getAllVisibleItems() as $item)
					{
						$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						if($product->getData('reward_point_product'))
						{
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::PURCHASE_PRODUCT, 'amount'=>((int)$product->getData('reward_point_product')) * $item->getQty(), 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$product->getId()."|".$order->getId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);
							$_customer->saveTransactionHistory($historyData);
						}
					}
				}
				 
				//Reward points to friend if this is first purchase
				if(Mage::helper('rewardpoints')->isInviteEnabled())
				{
					$strSesFrdId = Mage::getSingleton('customer/session')->getFriend();
					$strFrndId = $_customer->getAppthaFriendId();
					$strCusId = $_customer->getCustomerId();
					$objFrnd = Mage::getModel('rewardpoints/customer')->load($strFrndId);
					$orders = Mage::getModel("sales/order")->getCollection()
					->addFieldToFilter('customer_id',$customer->getId());					
					if(((sizeof($orders) == 1) && $strFrndId) || ($strSesFrdId && (sizeof($orders) == 1))){
						if(Mage::getStoreConfig('rewardpoints/earning_points/friend_first_purchase')){
							$point = explode('/',Mage::getStoreConfig('rewardpoints/earning_points/friend_first_purchase'));
							$_point = $point[0];
							if(sizeof($point)==2)
							{
								$total = $order->getGrandTotal;
								$_point = ((int)($total / $point[1])) * $point[0];
							}else{
								$_point = Mage::getStoreConfig('rewardpoints/earning_points/friend_first_purchase');
							}
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::FRIEND_FIRST_PURCHASE, 'amount'=>(int)$_point, 'balance'=>$objFrnd->getAppthaRewardPoint(), 'transaction_detail'=>$customer->getId()."|".$order->getId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);
							$objFrnd->saveTransactionHistory($historyData);
						}

						//Reward points to friend if this is second purchase
					}else if(((sizeof($orders) == 2) && $strFrndId) || ($strSesFrdId && (sizeof($orders) == 2))){
						if($strFrndId && Mage::getStoreConfig('rewardpoints/earning_points/friend_second_purchase')){
							$point = explode('/',Mage::getStoreConfig('rewardpoints/earning_points/friend_second_purchase'));
							$_point = $point[0];
							if(sizeof($point)== 2)
							{
								$total = $order->getGrandTotal;
								$_point = ((int)($total / $point[1])) * $point[0];
							}else{
								$_point = Mage::getStoreConfig('rewardpoints/earning_points/friend_second_purchase');
							}
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::FRIEND_NEXT_PURCHASE, 'amount'=>(int)$_point, 'balance'=>$objFrnd->getAppthaRewardPoint(), 'transaction_detail'=>$customer->getId()."|".$order->getId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);
							$objFrnd->saveTransactionHistory($historyData);
						}
					}
				}
			}
		}
	}
	public function saveOrderInvoiceAfter($argv)
	{
		$invoice = $argv->getInvoice();
		$order = $invoice->getOrder();
		$customerId = $order->getCustomerId();
		$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
		 
		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
		->addFieldToFilter('customer_id',$customerId)
		->addFieldToFilter('status',Apptha_Rewardpoints_Model_Status::PENDING)
		->addOrder('transaction_time','ASC')
		->addOrder('history_id','ASC')
		;

		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//Points for product
				case Apptha_Rewardpoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;

					$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setStatus($status)->save();
					break;
						
					//Add points when first purchase, next purchase
				case Apptha_Rewardpoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case Apptha_Rewardpoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
						
					$order = Mage::getModel('sales/order')->load($detail[1]);

					$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setStatus($status)->save();
					break;
						
					//Use points to check out
				case Apptha_Rewardpoints_Model_Type::USE_TO_CHECKOUT:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
					$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
					$transaction->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
						
					//Reward points for order
				case Apptha_Rewardpoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
					$status = Apptha_Rewardpoints_Model_Status::COMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setStatus($status)->save();
					break;
			}
		}
			
	}
	public function paymentCancel($arvgs)
	{
		$payment = $arvgs->getPayment();
		$order = $payment->getOrder();
		 		 
		$customerId = $order->getCustomerId();
		$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
		 
		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
		->addFieldToFilter('customer_id',$customerId)
		->addFieldToFilter('status',Apptha_Rewardpoints_Model_Status::PENDING)
		->addOrder('transaction_time','ASC')
		->addOrder('history_id','ASC')
		;

		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
				//Points for product
				case Apptha_Rewardpoints_Model_Type::PURCHASE_PRODUCT:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;

					$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now())->setBalance($customer->getRewardPoint());
					$transaction->setStatus($status)->save();
					break;
						
					//Add points when first purchase, next purchase
				case Apptha_Rewardpoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case Apptha_Rewardpoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getIncrementId()) continue;
					$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
						
					//Use points to check out
				case Apptha_Rewardpoints_Model_Type::USE_TO_CHECKOUT:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
					$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
					$transaction->setTransactionTime(now());
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setStatus($status)->save();
					break;
						
					//Reward points for order
				case Apptha_Rewardpoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
					$status = Apptha_Rewardpoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			}
		}
	}
	public function checkoutSuccess()
	{
		//Reset Reward Points in Session
		Mage::getSingleton('checkout/session')->unsetData('reward_points');
		Mage::getSingleton('checkout/session')->unsetData('discount');
	}
	
	//sales_quote_item_save_after
	public function itemSaveAfter($argv){
		if(Mage::helper('rewardpoints')->moduleEnabled())
		{
			//success message for product
			if(Mage::helper('rewardpoints')->isPrdRewdPointEnabled()){
				$arrObserverItem = $argv->getEvent()->getItem();
				$arrProduct = $arrObserverItem->getData();
				$strPrdId = $arrProduct['product_id'];					
				$strPrdQty = $arrProduct['qty'];
				$options = $arrObserverItem->getOptions();
				$objCatPrd = Mage::getModel('catalog/product')->load($strPrdId);
				$strPrdName = $objCatPrd->getname();
				$strPrdRewPont = $objCatPrd->getData('reward_point_product');
				$strOrdTotal = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
				if($strPrdQty > 1)$strPrdRewPont = ((int)$strPrdRewPont * $strPrdQty);
				if($strPrdId && ($strPrdRewPont > 0) && $strOrdTotal){
					Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('rewardpoints')->__('You will earn %s %s if you purchase this ( %s ) product.',$strPrdRewPont, Mage::helper('rewardpoints')->getPointCurency(),$strPrdName));
				}
			}
			//success message for purchase order
			if(Mage::helper('rewardpoints')->isOrdRewdPointEnabled())
			{
				$arrRewardOrder = Mage::helper('rewardpoints')->isOrdRewdPointEnabled();
				$arrRewPoint = explode('/',$arrRewardOrder);
				if(sizeof($arrRewPoint)==2)
				{
					$strOrdTotal = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
					$strRewPoint = ($strOrdTotal / $arrRewPoint[1]) * $arrRewPoint[0];
				}
				if($strRewPoint > 0){
					Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('rewardpoints')->__('You will earn %s %s if you complete this order.',$strRewPoint, Mage::helper('rewardpoints')->getPointCurency()));					
				}
			}

		}
	}
}
<?php
class Apptha_Rewardpoints_RewardpointsController extends Mage_Core_Controller_Front_Action
{
	const EMAIL_TO_SEMDER_TEMPLATE_XML_PATH 	= 'rewardpoints/share_points/sender_template';
	const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'rewardpoints/share_points/recipient_template';
	const XML_PATH_EMAIL_IDENTITY				= 'rewardpoints/share_points/email_sender';
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	protected function _getHelper()
	{
		return Mage::helper('rewardpoints');
	}

	protected function _sendEmailTransaction($emailto, $name, $template, $data)
	{
		$storeId = Mage::app()->getStore()->getId();
		$templateId = Mage::getStoreConfig($template,$storeId);			
		$translate  = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		try{
			Mage::getModel('core/email_template')
			->sendTransactional(
			$templateId,
			Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId),
			$emailto,
			$name,
			$data,
			$storeId);
			$translate->setTranslateInline(true);
		}catch(Exception $e){
			$this->_getSession()->addError($this->__("Email can not send"));
		}
	}
	
	//prepare mail for recipient
	public function _mailToRecipient($formData,$_customer){
		$mailto = $formData['email'];
		$name = $formData['name'];
		$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
		$postObject = new Varien_Object();
		$postObject->setData($this->getRequest()->getPost());
		$postObject->setSender($_customer->getCustomerModel());
		$postObject->setData('login_link',Mage::getUrl('customer/account/login'));
		$postObject->setData('register_link',Mage::getUrl('customer/account/create'));
		$this->_sendEmailTransaction($mailto, $name, $template, $postObject->getData());
	} 
	//prepare mail for sender
	public function _mailToSender($formData,$_customer){
		$mailto = $_customer->getCustomerModel()->getEmail();
		$name = $_customer->getCustomerModel()->getName();
		$template = self::EMAIL_TO_SEMDER_TEMPLATE_XML_PATH;
		$postObject = new Varien_Object();
		$postObject->setData(array('points' => $formData['points'],'login_link' => Mage::getUrl('customer/account/login'),'name' => $name));
		$this->_sendEmailTransaction($mailto, $name, $template, $postObject->getData());
	}


	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		// a brute-force protection here would be nice

		parent::preDispatch();

		if (!$this->getRequest()->isDispatched()) {
			return;
		}

		$action = $this->getRequest()->getActionName();
		if (!preg_match('/^(create|login|logoutSuccess|forgotpassword|forgotpasswordpost|confirm|confirmation)/i', $action)) {
			if (!$this->_getSession()->authenticate($this)) {
				$this->setFlag('', 'no-dispatch', true);
			}
		} else {
			$this->_getSession()->setNoReferer(true);
		}
	}
	public function indexAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Referral Points Management'));
		Mage::dispatchEvent('rewardpoints_manager_index',array(
           'model'    => $this->_getSession()->getCustomer()
		));
		$this->renderLayout();
	}

	public function sendAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}

		if(!Mage::helper('rewardpoints')->sharePointsToFriend()){
			$this->norouteAction();
			return;
		}
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');

		if($this->getRequest()->getPost()){
			$data = $this->getRequest()->getPost();
			//validate email
			$validator = new Zend_Validate_EmailAddress();
			$strInvalEmail = $validator->isValid($this->getRequest()->getPost("email"));
			if(!$strInvalEmail){
				$this->_getSession()->addError($this->__("Invalid Email!"));
				$this->_redirect('rewardpoints/rewardpoints/index');
			}
			//check with captcha
			if($this->_getHelper()->enabledCapcha()){
				$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
				require($require);
				$img = new Securimage();
				$valid = $img->check($this->getRequest()->getPost("code"));
			}else{
				$valid = true;
			}
			if($valid)
			{
				//current customer
				$_customer = Mage::getModel('rewardpoints/customer')->load($this->_getSession()->getCustomer()->getId());
				$point = $this->getRequest()->getPost("points");
				$objValidInt = new Zend_Validate_Int();
				if ($objValidInt->isValid($point)) {				
				if($point < 0 ) $point = -$point;
				if($_customer->getAppthaRewardPoint() >= $point)
				{
					//send reward point
					$store_id = Mage::helper('core')->getStoreId();
					$website_id = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
					$customer = Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($this->getRequest()->getPost("email"));
					if($customer->getId()!=$_customer->getId())
					{
						if($customer->getId()){
							//Add reward points to friend
							$appthaCustomer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::RECIVE_FROM_FRIEND, 'amount'=>$point,'balance'=>$appthaCustomer->getAppthaRewardPoint(), 'transaction_detail'=>$_customer->getId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
							$appthaCustomer->saveTransactionHistory($historyData);
							$appthaCustomer->addRewardPoint($point);

							//Subtract reward points of current customer
							$_customer->addRewardPoint(-$point);
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SEND_TO_FRIEND, 'amount'=>$point,'balance'=>$_customer->getAppthaRewardPoint() , 'transaction_detail'=>$customer->getId(), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::COMPLETE);
							$_customer->saveTransactionHistory($historyData);

							$this->_getSession()->addSuccess($this->__("Your reward points were sent successfuly"));
							//Send an Email to Recipient (friend)
							if(Mage::getStoreConfig('rewardpoints/share_points/recipient_template'))
							{
								$formData = $this->getRequest()->getPost();
								$this->_mailToRecipient($formData,$_customer);
							}
							//Send an Email to Customer (logged customer)
							if(Mage::getStoreConfig('rewardpoints/share_points/sender_template'))
							{
								$formData = $this->getRequest()->getPost();
								$this->_mailToSender($formData,$_customer);
							}
							$this->_redirect('rewardpoints/rewardpoints/index');
						}else{																				
							//customer dose not exist
							//Send an Email to Recipient (friend)
							if(Mage::getStoreConfig('rewardpoints/share_points/recipient_template'))
							{
								$formData = $this->getRequest()->getPost();
								$this->_mailToRecipient($formData,$_customer);
							}
							//Send an Email to Customer (logged customer)
							if(Mage::getStoreConfig('rewardpoints/share_points/sender_template'))
							{
								$formData = $this->getRequest()->getPost();
								$this->_mailToSender($formData,$_customer);
							}
							//Subtract reward points of current customer							
							$_customer->addRewardPoint(-$point);
							$historyData = array('type_of_transaction'=>Apptha_Rewardpoints_Model_Type::SEND_TO_FRIEND, 'amount'=>$point, 'balance'=>$_customer->getAppthaRewardPoint(), 'transaction_detail'=>$this->getRequest()->getPost("email"), 'transaction_time'=>now(), 'status'=>Apptha_Rewardpoints_Model_Status::PENDING);
							$_customer->saveTransactionHistory($historyData);							
							$this->_getSession()->addSuccess($this->__("Your reward points were sent successfully"));
							//$this->_getSession()->addError($this->__("Customer email does not exit!"));
							$this->_redirect('rewardpoints/rewardpoints/index');
						}
					}else
					{	//Customer send reward points yourself
						$this->_getSession()->addError($this->__("You can not send reward points to yourself"));
					}
				}else{
					//Current total reward points do not enought to send
					$this->_getSession()->addError($this->__("You do not have enough points to send to your friend"));
				}
			}else{
				//captcha mismatch
				$this->_getSession()->addError($this->__("Your security code is incorrect"));
			}
			}
		}else{ //nothing post to data
			$this->_getSession()->addError($this->__("You do not have permission!"));
		}
		$this->_redirect('rewardpoints/rewardpoints/index');
	}

	//captcha settings
	public function imageAction()
	{
		if(!Mage::helper('rewardpoints')->sharePointsToFriend()){
			$this->norouteAction();
			return;
		}
		$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
		require($require);
		$hp = $this->_getHelper();
		$img = new Securimage();

		//Change some settings
		$img->use_wordlist = $hp->capchaUseWordList();
		$img->image_width = $hp->getCapchaImageWidth();
		$img->image_height = $hp->getCapchaImageHeight();
		$img->perturbation =$hp->getCapchaPerturbation();
		$img->code_length = $hp->getCapchaCodeLength();
		$img->image_bg_color = new Securimage_Color($hp->getCapchaBackgroundColor());
		$img->use_transparent_text = $hp->capchaUseTransparentText();
		$img->text_transparency_percentage = $hp->getCapchaTextTransparencyPercentage(); // 100 = completely transparent
		$img->num_lines = $hp->getCapchaNumberLine();
		$img->text_color = new Securimage_Color($hp->getCapchaTextColor());
		$img->line_color = new Securimage_Color($hp->getCapchaLineColor());
		$backgroundFile = $hp->getCapchaBackgroundImage();
		$img->show($backgroundFile);
	}
}
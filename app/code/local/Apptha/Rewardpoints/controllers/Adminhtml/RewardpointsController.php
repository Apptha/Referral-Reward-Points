<?php

class Apptha_Rewardpoints_Adminhtml_RewardpointsController extends Mage_Adminhtml_Controller_action
{
    protected function _initCustomer($idFieldName = 'id')
    {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('rewardpoints/rewardpointsmanager')
			->_addBreadcrumb(Mage::helper('rewardpoints')->__('Reward Points & Reward Manager'), Mage::helper('rewardpoints')->__('Reward Points & Reward Manager'));
		
		return $this;
	}
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	public function transactionAction()
	{
		$this->_initCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByCustomer(Mage::registry('current_customer'));

        Mage::register('subscriber', $subscriber);
        $this->getResponse()->setBody($this->getLayout()->createBlock('rewardpoints/adminhtml_customer_edit_tab_rewardpoints_grid')->toHtml());
	}
	public function indexAction()
	{
		$collection = Mage::getModel('rewardpoints/customer')->getcollection();
		
		$this->_initAction();
		//$block = $this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints');
		//$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}
	
	public function exportCsvAction()
    {
        $fileName   = 'rewardpoints.csv';
        $content    = $this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function importAction()
    {
    	$this->loadLayout()->_setActiveMenu('rewardpoints/rewardpointsmanager');
    	$this->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_edit'))
				->_addLeft($this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_edit_tabs'));
		$this->renderLayout();
    }
    
    public function saveAction()
    {
	    if($_FILES['filename']['name'] != '') {
			try {
				/* Starting upload */	
				$uploader = new Varien_File_Uploader('filename');
				
				// Any extention would work
		        $uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(false);
				
				// Set the file upload mode 
				// false -> get the file directly in the specified folder
				// true -> get the file in the product like folders 
				//	(file.jpg will go in something like /media/f/i/file.jpg)
				$uploader->setFilesDispersion(false);
						
				// We set media as the upload dir
				$path = Mage::getBaseDir('media').DS;
				$uploader->save($path, $_FILES['filename']['name'] );
				$filename = $path.$uploader->getUploadedFileName();
				
				$fp = @fopen($filename,'r');
				$line = 1;
				$errors = array();
				if($fp){
					$website_id = $this->getRequest()->getParam('website_id');
					
					while (!feof($fp)) {
						
						$tmp = fgets($fp); //Reading a file line by line
						if($line >1){
							$content = str_replace('"','',$tmp);							
							$customerInfo = explode(';',$content);							
							$customer = Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($customerInfo[1]);								
							if(sizeof($customerInfo) ==3)
							{
								$customer = Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($customerInfo[1]);
								if($customer->getId())
								{
								  	$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
								  	$customerInfo[2] = trim($customerInfo[2]);
								  	$objValidInt = new Zend_Validate_Int();
								  	if($objValidInt->isValid($customerInfo[2])){								  		
								  		$_customer->setData('apptha_reward_point',$customerInfo[2]);
								  		$_customer->save();
								  	}else
								  	{
								  		$errors[] = Mage::helper('rewardpoints')->__('At rows %s reward points must be numeric',$customerInfo[2]);
								  	}
								}else
								{
									$errors[] = Mage::helper('rewardpoints')->__('At rows %s customer is not avaiable',$customerInfo[1]);
								}
							}
						}
						$line  ++;
					}
					
					if(sizeof($errors))
					{
						$err =Mage::helper('rewardpoints')->__("Some errors occur while importing points")."<br>";
						foreach($errors as $error)
							$err .= $error."<br>";
						Mage::getSingleton('adminhtml/session')->addError($err);
					}
					fclose($fp);
					@unlink($filename);
					if(!sizeof($errors))
					{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('Your file was imported successfuly'));					
					}
					$this->_redirect("*/*/");
				}
			} catch (Exception $e) {
		      	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		      	$this->_redirect("*/*/import");
		    }
    	}else
    	{
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__("Please select a file to import"));
    		$this->_redirect("*/*/import");
    	}
    }
}
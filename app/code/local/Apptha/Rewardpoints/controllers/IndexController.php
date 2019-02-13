<?php
class Apptha_Rewardpoints_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
	//action for invite friend via referral link
	public function indexAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		if(!Mage::helper('rewardpoints')->isInviteEnabled()){
			$this->norouteAction();
			return;
		}
		if(Mage::helper('rewardpoints')->isRefLinkEnabled())
		{
			$invite = $this->getRequest()->getParam('c');
			//$strRefKey = $this->getRequest()->getParam('referral_key');
			//$objInvColl = Mage::getModel('rewardpoints/invitations')->getCollection();
			$customer = Mage::getModel('customer/customer');
			$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($invite);
			//$arrInvColl = $objInvColl->getResult($strRefKey,$customer->getId());
			$objValEmail = new Zend_Validate_EmailAddress();
				
			if ($objValEmail->isValid($invite)) {
				//points add to customer
				Mage::dispatchEvent('invitation_referral_link_click',array('invite'=>$invite,'request'=>$this->getRequest()));
			}
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('Thank you for visiting our site'));
		$this->_redirectUrl(Mage::getUrl('customer/account/login'));
	}
}
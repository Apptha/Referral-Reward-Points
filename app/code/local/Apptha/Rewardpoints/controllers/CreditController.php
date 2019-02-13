<?php
class Apptha_Rewardpoints_CreditController extends Mage_Core_Controller_Front_Action
{
    public function checkAction()
    {
		if(Mage::helper('rewardpoints')->getCreditModule())
		{
			$this->getResponse()->setBody("1");
		}else{
			$this->getResponse()->setBody("0");
		}
    }
}
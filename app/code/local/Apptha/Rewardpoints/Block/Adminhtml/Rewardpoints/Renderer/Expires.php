<?php
class Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Renderer_Expires extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	//render the data from grid
	public function render(Varien_Object $data)
	{
		$strData  =  $data->getData($this->getColumn()->getIndex());
		$objValidInt = new Zend_Validate_Int();
		$objValidDate = new Zend_Validate_Date('YYYY-MM-DD H:i:s');
		//check int value
		if($objValidInt->isValid($strData)){
			$arrTrans = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('customer_id',$strData)
			->addOrder('history_id','DESC')
			;
			if(sizeof($arrTrans))foreach($arrTrans as $arrTransVal){
				return $strRes = Mage::Helper('rewardpoints')->getExpDate($arrTransVal->getTransactionTime());
			}
		//check date value
		}else if($objValidDate->isValid($strData)){
			return Mage::Helper('rewardpoints')->getExpDate($strData);							
		}
			
	}

}

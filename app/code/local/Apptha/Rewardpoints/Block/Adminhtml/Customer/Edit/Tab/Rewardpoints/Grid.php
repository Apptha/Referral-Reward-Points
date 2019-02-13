<?php

class Apptha_Rewardpoints_Block_Adminhtml_Customer_Edit_Tab_Rewardpoints_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('Rewardpoints_Grid');
		$this->setDefaultSort('transaction_time');
		$this->setDefaultDir('desc');

		$this->setUseAjax(true);
		$this->setTemplate('apptha_rewardpoints/grid.phtml');
		$this->setEmptyText(Mage::helper('rewardpoints')->__('No Transaction Found'));
	}

	public function getGridUrl()
	{
		return $this->getUrl('rewardpoints/adminhtml_rewardpoints/transaction', array('id'=>Mage::registry('current_customer')->getId()));

	}

	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('rewardpoints/rewardpointshistory_collection')
		->addFieldToFilter('customer_id',Mage::registry('current_customer')->getId());

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('history_id', array(
            'header'    =>  Mage::helper('rewardpoints')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  10
		));

		$this->addColumn('type_of_transaction', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Transaction Type'),
        	'type'		=>	'options',
            'align'     =>  'left',
            'index'     =>  'type_of_transaction',
        	'options'	=>  Mage::getModel('rewardpoints/type')->getOptionArray()
		));

		$this->addColumn('amount', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Amount'),
            'align'     =>  'left',
            'index'     =>  'amount',
		));

		$this->addColumn('balance', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Balance'),
            'align'     =>  'left',
            'index'     =>  'balance',
		));
		$this->addColumn('transaction_detail', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Transaction Details'),
            'align'     =>  'left',
        	'width'		=>  400,
            'index'     =>  'transaction_detail',
		));

		$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
            ));

       /*$this->addColumn('expires', array(
          'header'    => Mage::helper('rewardpoints')->__('Expires'),
          'align'     => 'left',
          'width'     => '80px',
          'renderer'  => 'Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Renderer_Expires',
          'type'      => 'text',
          'index'     => 'transaction_time',
            ));*/

       $this->addColumn('status', array(
            'header'    =>  Mage::helper('rewardpoints')->__('Status'),
        	'type'		=> 'options',
            'align'     =>  'center',
            'index'  	=>  'status',
        	'options'	=>	Mage::getModel('rewardpoints/status')->getOptionArray()
            ));

            return parent::_prepareColumns();
	}

}

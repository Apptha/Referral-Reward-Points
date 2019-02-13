<?php

class Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('rewardpointsGrid');
      $this->setDefaultSort('customer_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      //$this->setRowClickCallback(null);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('rewardpoints/customer')->getCollection();
      $collection->join('customer/entity','`customer/entity`.entity_id = `main_table`.customer_id');      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('customer_id', array(
          'header'    => Mage::helper('rewardpoints')->__('ID'),
          'align'     =>'center',
          'width'     => '100px',
          'index'     => 'customer_id',
      ));
      
      $this->addColumn('email', array(
          'header'    => Mage::helper('rewardpoints')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));

      $this->addColumn('reward_point', array(
          'header'    => Mage::helper('rewardpoints')->__('Referral Reward Points'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'apptha_reward_point',
          'type'      => 'text',
      ));
      
      /*$this->addColumn('expires', array(
          'header'    => Mage::helper('rewardpoints')->__('Expires'),
          'align'     => 'left',
          'width'     => '80px',
          'renderer'  => 'Apptha_Rewardpoints_Block_Adminhtml_Rewardpoints_Renderer_Expires',
          'type'      => 'text',
          'index'     => 'customer_id',
      ));*/
		
		$this->addExportType('*/*/exportCsv', Mage::helper('rewardpoints')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('rewardpoints')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
       
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getId()));
  }

}
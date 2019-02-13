<?php

class Apptha_Rewardpoints_Block_Adminhtml_Customer_Edit_Tab_Rewardpoints extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('apptha_rewardpoints/customer/tab/rewardpoints.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_rewardpoints');
        $customer = Mage::registry('current_customer');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('rewardpoints')->__('Referral Reward Points Information')));

        $fieldset->addField('rewardpoints', 'text',
             array(
                    'label' 	=> Mage::helper('rewardpoints')->__('Referral Reward Points'),
                    'name'  	=> 'apptha_reward_points',
             		'disabled'	=> 'disabled',
             )
        );
        
        $fieldset1 = $form->addFieldset('addpoints_fieldset', array('legend'=>Mage::helper('rewardpoints')->__('Change Referral Reward Points Of Customer'),'style'=>'display:none'));
        
        $fieldset1->addField('amount', 'text',
             array(
                    'label' 	=> Mage::helper('rewardpoints')->__('Amount'),
                    'name'  	=> 'reward_points_amount',
             		'class'		=> 'validate-digits'
             )
        );
        
        $fieldset1->addField('action', 'select',
             array(
                    'label' 	=> Mage::helper('rewardpoints')->__('Action'),
                    'name'  	=> 'reward_points_action',
             		'options'	=> Mage::getModel('rewardpoints/action')->getOptionArray()
             )
        );
        
        $fieldset1->addField('comment', 'textarea',
             array(
                    'label' 	=> Mage::helper('rewardpoints')->__('Comment'),
                    'name'  	=> 'reward_points_comment',
             		'style'		=>	'height:100px'
             )
        );
        
        if ($customer->isReadonly()) {
            $form->getElement('rewardpoints')->setReadonly(true, true);
        }

        if(Mage::registry('current_customer'))
        {
        	$points = Mage::getModel('rewardpoints/customer')->load($customer->getId())->getData('apptha_reward_point');
        	$form->getElement('rewardpoints')->setValue($points);
        	$form->getElement('action')->setValue(1);
        }
        $this->setForm($form);
        return $this;
    }


    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('rewardpoints/adminhtml_customer_edit_tab_rewardpoints_grid','rewardpoints.grid')
        );
        return parent::_prepareLayout();
    }

}

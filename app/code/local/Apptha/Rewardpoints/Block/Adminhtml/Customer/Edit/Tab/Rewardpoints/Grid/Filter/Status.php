<?php
/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected static $_statuses;

    public function __construct()
    {
        self::$_statuses = array(
                null                                        => null,
                Mage_Newsletter_Model_Queue::STATUS_SENT    => Mage::helper('customer')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL  => Mage::helper('customer')->__('Cancel'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER   => Mage::helper('customer')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('customer')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE   => Mage::helper('customer')->__('Paused'),
            );
        parent::__construct();
    }

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>Mage::helper('customer')->__($label));
        }

        return $result;
    }

    public function getCondition()
    {
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}

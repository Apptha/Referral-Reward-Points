<?php

class Apptha_Rewardpoints_Helper_Paypal extends Mage_Paypal_Helper_Data
{
	public function prepareLineItems(Mage_Core_Model_Abstract $salesEntity, $discountTotalAsItem = true, $shippingTotalAsItem = false)
    {
    	$items = array();
        foreach ($salesEntity->getAllItems() as $item) {
            if (!$item->getParentItem()) {
                $items[] = new Varien_Object($this->_prepareLineItemFields($salesEntity, $item));
            }
        }
        $discountAmount = 0; // this amount always includes the shipping discount
        $shippingDescription = '';
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $discountAmount = abs(1 * $salesEntity->getBaseDiscountAmount() +Mage::getSingleton('checkout/session')->getDiscount());
            $shippingDescription = $salesEntity->getShippingDescription();
            $totals = array(
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $salesEntity->getBaseTaxAmount(),
                'shipping' => $salesEntity->getBaseShippingAmount(),
//                'shipping_discount' => -1 * abs($salesEntity->getBaseShippingDiscountAmount()),
            );
        } else {
            $address = $salesEntity->getIsVirtual() ? $salesEntity->getBillingAddress() : $salesEntity->getShippingAddress();
            $discountAmount = abs(1 * $address->getBaseDiscountAmount()+Mage::getSingleton('checkout/session')->getDiscount());
            $shippingDescription = $address->getShippingDescription();
            $totals = array (
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $address->getBaseTaxAmount(),
                'shipping' => $address->getBaseShippingAmount(),
                'discount' => $discountAmount,
//                'shipping_discount' => -1 * abs($address->getBaseShippingDiscountAmount()),
            );
        }
        // discount total as line item (negative)
        if ($discountTotalAsItem && $discountAmount) {
            $items[] = new Varien_Object(array(
                'name'   => Mage::helper('paypal')->__('Discount'),
                'qty'    => 1,
                'amount' => -1.00 * $discountAmount,
            ));
        }
        Mage::log($discountAmount."|".Mage::getSingleton('checkout/session')->getDiscount());
        // shipping total as line item
        if ($shippingTotalAsItem && (!$salesEntity->getIsVirtual()) && (float)$totals['shipping']) {
            $items[] = new Varien_Object(array(
                'id'     => Mage::helper('paypal')->__('Shipping'),
                'name'   => $shippingDescription,
                'qty'    => 1,
                'amount' => (float)$totals['shipping'],
            ));
        }
        
        return array($items, $totals, $discountAmount, $totals['shipping']);
    }
}
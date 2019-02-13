<?php
class Apptha_Rewardpoints_Model_Tax_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    /**
     * Calculate address total tax based on address subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _totalBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items      = $address->getAllItems();
        $store      = $address->getQuote()->getStore();
        $taxGroups  = array();

        $inclTax = false;
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $taxGroups[(string)$rate]['applied_rates'] = $this->_calculator->getAppliedRates($taxRateRequest);
                    $this->_aggregateTaxPerRate($child, $rate, $taxGroups);
                    $this->_getAddress()->addTotalAmount('hidden_tax', $child->getHiddenTaxAmount());
                    $this->_getAddress()->addBaseTotalAmount('hidden_tax', $child->getBaseHiddenTaxAmount());
                    $inclTax = $child->getIsPriceInclTax();
                }
                $this->_recalculateParent($item);
            } else {

                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $taxGroups[(string)$rate]['applied_rates'] = $this->_calculator->getAppliedRates($taxRateRequest);
                $this->_aggregateTaxPerRate($item, $rate, $taxGroups);
                $this->_getAddress()->addTotalAmount('hidden_tax', $item->getHiddenTaxAmount());
                $this->_getAddress()->addBaseTotalAmount('hidden_tax', $item->getBaseHiddenTaxAmount());
                $inclTax = $item->getIsPriceInclTax();
            }
        }

        foreach ($taxGroups as $rateKey => $data) {
            $rate = (float) $rateKey;
            $totalTax = $this->_calculator->calcTaxAmount(array_sum($data['totals']), $rate, $inclTax);
            $baseTotalTax = $this->_calculator->calcTaxAmount(array_sum($data['base_totals']), $rate, $inclTax);
            
            if(Mage::getStoreConfig('tax/calculation/apply_after_discount')){
	            $totalRewardPointDiscount = $baseTotalRewardPointDiscount = Mage::getSingleton('checkout/session')->getDiscount();
	            $totalRewardPointDiscount = $this->_calculator->calcTaxAmount($totalRewardPointDiscount, $rate, $inclTax);
	            $baseTotalRewardPointDiscount = $this->_calculator->calcTaxAmount($baseTotalRewardPointDiscount, $rate, $inclTax);
	            if($totalRewardPointDiscount > $totalTax) $totalRewardPointDiscount = $totalTax;
	            if($baseTotalRewardPointDiscount > $baseTotalTax) $baseTotalRewardPointDiscount = $baseTotalTax;
            }else {$totalRewardPointDiscount = $baseTotalRewardPointDiscount = 0;}
            
            $this->_addAmount($totalTax - $totalRewardPointDiscount);
            $this->_addBaseAmount($baseTotalTax - $baseTotalRewardPointDiscount);
            $this->_saveAppliedTaxes($address, $data['applied_rates'], $totalTax, $baseTotalTax, $rate);
        }
        return $this;
    }
}
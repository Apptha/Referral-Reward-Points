<?php

class Apptha_Rewardpoints_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
	protected function insertTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        
    	$rewardOrder = Mage::getModel('rewardpoints/rewardpointsorder')->load($source->getOrder()->getId());
		$rewardpoints = $rewardOrder->getRewardPoint();
		if($rewardpoints){
			$money = $rewardOrder->getMoney();
			$lineBlock['lines'][] = array(
                        array(
                            'text'      => Mage::helper('rewardpoints')->__('Spent Reward Points'),
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => 7,
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => Mage::helper('rewardpoints')->formatPoints($rewardpoints),
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => 7,
                            'font'      => 'bold'
                        ),
                    );
              $lineBlock['lines'][] = array(
                        array(
                            'text'      => Mage::helper('rewardpoints')->__('Referral Points Discount'),
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => 7,
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => Mage::helper('rewardpoints')->formatMoney(-$money,true,false),
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => 7,
                            'font'      => 'bold'
                        ),
                    );
		}
		
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
                }
            }
        }

        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}

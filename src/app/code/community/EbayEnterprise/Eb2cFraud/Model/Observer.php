<?php
/**
 * Copyright (c) 2013-2014 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2014 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class EbayEnterprise_Eb2cFraud_Model_Observer
{
	/** @var EbayEnterprise_Eb2cFraud_Helper_Data */
	protected $_helper;
	/** @var EbayEnterprise_Eb2cFraud_Helper_Config */
	protected $_config;
	/** @var EbayEnterprise_RiskInsight_Model_Risk_Order */
	protected $_riskOrder;

	/**
	 * @param array $initParams optional keys:
	 *                          - 'helper' => EbayEnterprise_Eb2cFraud_Helper_Data
	 *                          - 'config' => EbayEnterprise_Eb2cFraud_Helper_Config
	 */
	public function __construct(array $initParams=array())
	{
		$this->_riskOrder = Mage::getModel('eb2cfraud/risk_order');

		list($this->_helper, $this->_config) = $this->_checkTypes(
			$this->_nullCoalesce($initParams, 'helper', Mage::helper('eb2cfraud')),
			$this->_nullCoalesce($initParams, 'config', Mage::helper('eb2cfraud/config'))
		);
	}

	/**
	 * Type checks for self::__construct $initParams
	 *
	 * @param  EbayEnterprise_Eb2cFraud_Helper_Data
	 * @param  EbayEnterprise_Eb2cFraud_Helper_Config
	 * @return array
	 */
	protected function _checkTypes(
		EbayEnterprise_Eb2cFraud_Helper_Data $helper,
		EbayEnterprise_Eb2cFraud_Helper_Config $config
	) {
		return array($helper, $config);
	}
	/**
	 * Return the value at field in array if it exists. Otherwise, use the
	 * default value.
	 *
	 * @param array      $arr
	 * @param string|int $field Valid array key
	 * @param mixed      $default
	 * @return mixed
	 */
	protected function _nullCoalesce(array $arr, $field, $default)
	{
		return isset($arr[$field]) ? $arr[$field] : $default;
	}

	/**
	 * @param  mixed
	 * @return bool
	 */
	protected function _isValidOrder($order=null)
	{
		return ($order && $order instanceof Mage_Sales_Model_Order);
	}

	/**
	 * Handle multi-shipping orders.
	 *
	 * @param  Varien_Event_Observer
	 * @return self
	 */
	public function handleCheckoutSubmitAllAfter(Varien_Event_Observer $observer)
	{
		$orders = (array) $observer->getEvent()->getOrders();
		
		if( !empty($orders))
		{
			foreach ($orders as $index => $order) {
				if ($this->_isValidOrder($order)) {
					$this->_riskOrder->processRiskOrder($order, $observer);
				} else {
					$logMessage = sprintf('[%s] No sales/order instances was found.', __CLASS__);
                        		$this->_helper->logWarning($logMessage);
				}
			}
		} else {
			$order = $observer->getEvent()->getOrder();
		 	if ($this->_isValidOrder($order)) {
                        	$this->_riskOrder->processRiskOrder($order, $observer);
                        } else {
                                $logMessage = sprintf('[%s] No sales/order instances was found.', __CLASS__);
                                $this->_helper->logWarning($logMessage);
                        }
		}

		return $this;
	}

	/**
	  * Log Removed Cart Items
          * @param   Varien_Event_Observer
	  * @return  self
	  */
	public function addRemovedCartCount(Varien_Event_Observer $observer )
	{
		$previous = 0;
		$previous = Mage::getSingleton('core/session')->getPrevItemQuoteRemoval();
		$previous++;
		
		Mage::getSingleton('core/session')->setPrevItemQuoteRemoval($previous);
	}

    	public function updateOrderStatus(Varien_Event_Observer $observer)
    	{
    	    $event = $observer->getEvent()->getPayload();

    	    $orderId = $event->getCustomerOrderId();
	    $responseCode = $event->getResponseCode();

	    $order = Mage::getModel("sales/order")->loadByIncrementId($orderId);

	    Mage::Log("RiskAssessmentReply Payload: ". $event->serialize());

	    if( $order->getId() )
	    {
		$order->setState($this->_config->getOrderStateForResponseCode($responseCode), true);
		$order->setStatus($this->_config->getOrderStatusForResponseCode($responseCode));
		$order->save();
	    }

	    return $this;
    	}
}

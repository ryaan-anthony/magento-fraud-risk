<?php
/**
 * Copyright (c) 2015 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the eBay Enterprise
 * Magento Extensions End User License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://www.ebayenterprise.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf
 *
 * @copyright   Copyright (c) 2015 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://www.ebayenterprise.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf  eBay Enterprise Magento Extensions End User License Agreement
 *
 */

/**
 * @codeCoverageIgnore
 */
class EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_Type
	extends EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_Type_Abstract
	implements EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_IType
{
	/** @var Mage_Sales_Model_Order */
	protected $_order;
	/** @var EbayEnterprise_Eb2cFraud_Helper_Data */
	protected $_helper;
	/** @var string | null */
	protected $_extractCardHolderName;
	/** @var string | null */
	protected $_extractPaymentAccountUniqueId;
	/** @var string | null */
	protected $_extractPaymentAccountBin;
	/** @var string | null */
	protected $_extractExpireDate;
	/** @var string | null */
	protected $_extractCardType;
	/** @var string | null */
	protected $_extractTransactionResponses;
	/** @var string */
	protected $_extractIsToken;

	/**
	 * @param array $initParams Must have this key:
	 *                          - 'order' => Mage_Sales_Model_Order
	 *                          - 'helper' => EbayEnterprise_Eb2cFraud_Helper_Data
	 */
	public function __construct(array $initParams=array())
	{
		list($this->_order, $this->_helper) = $this->_checkTypes(
			$this->_nullCoalesce($initParams, 'order', $initParams['order']),
			$this->_nullCoalesce($initParams, 'helper', Mage::helper('eb2cfraud'))
		);
		$this->_initialize();
	}

	/**
	 * Type hinting for self::__construct $initParams
	 *
	 * @param  Mage_Sales_Model_Order
	 * @param  EbayEnterprise_Eb2cFraud_Helper_Data
	 * @return array
	 */
	protected function _checkTypes(
		Mage_Sales_Model_Order $order,
		EbayEnterprise_Eb2cFraud_Helper_Data $helper
	) {
		return array($order, $helper);
	}

	/**
	 * Return the value at field in array if it exists. Otherwise, use the default value.
	 *
	 * @param  array
	 * @param  string | int $field Valid array key
	 * @param  mixed
	 * @return mixed
	 */
	protected function _nullCoalesce(array $arr, $field, $default)
	{
		return isset($arr[$field]) ? $arr[$field] : $default;
	}

	public function getExtractCardHolderName()
	{
		return $this->_extractCardHolderName;
	}

	public function setExtractCardHolderName($cardHolderName)
	{
		$this->_extractCardHolderName = $cardHolderName;
		return $this;
	}

	public function getExtractPaymentAccountUniqueId()
	{
		return $this->_extractPaymentAccountUniqueId;
	}

	public function setExtractPaymentAccountUniqueId($paymentAccountUniqueId)
	{
		$this->_extractPaymentAccountUniqueId = $paymentAccountUniqueId;
		return $this;
	}

	public function getExtractPaymentAccountBin()
	{
		return $this->_extractPaymentAccountBin;
	}

	public function setExtractPaymentAccountBin($paymentAccountBin)
	{
		$this->_extractPaymentAccountBin = $paymentAccountBin;
		return $this;
	}

	public function getExtractExpireDate()
	{
		return $this->_extractExpireDate;
	}

	public function setExtractExpireDate($expireDate)
	{
		$this->_extractExpireDate = $expireDate;
		return $this;
	}

	public function getExtractCardType()
	{
		return $this->_extractCardType;
	}

	public function setExtractCardType($cardType)
	{
		$this->_extractCardType = $cardType;
		return $this;
	}

	public function getExtractTransactionResponses()
	{
		return $this->_extractTransactionResponses;
	}

	public function setExtractTransactionResponses(array $transactionResponses)
	{
		$this->_extractTransactionResponses = $transactionResponses;
		return $this;
	}

	public function getExtractIsToken()
	{
		return $this->_extractIsToken;
	}

	public function setExtractIsToken($isToken)
	{
		$this->_extractIsToken = $isToken;
		return $this;
	}

	protected function _initialize()
	{
		return $this;
	}
}

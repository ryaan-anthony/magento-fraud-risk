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

class EbayEnterprise_RiskService_Sdk_Payment_Card
	extends EbayEnterprise_RiskService_Sdk_Payload
	implements EbayEnterprise_RiskService_Sdk_Payment_ICard
{
	/** @var string */
	protected $_cardHolderName;
	/** @var string */
	protected $_paymentAccountUniqueId;
	/** @var bool */
	protected $_isToken;
	/** @var string */
	protected $_paymentAccountBin;
	/** @var string */
	protected $_expireDate;
	/** @var string */
	protected $_cardType;

	public function __construct(array $initParams=array())
	{
		parent::__construct($initParams);
		$this->_optionalExtractionPaths = array(
			'setCardHolderName' => 'x:CardHolderName',
			'setPaymentAccountUniqueId' => 'x:PaymentAccountUniqueId',
			'setPaymentAccountBin' => 'x:PaymentAccountBin',
			'setExpireDate' => 'x:ExpireDate',
			'setCardType' => 'x:CardType',
		);
		$this->_booleanExtractionPaths = array(
			'setIsToken' => 'string(x:PaymentAccountUniqueId/@isToken)',
		);
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getCardHolderName()
	 */
	public function getCardHolderName()
	{
		return $this->_cardHolderName;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setCardHolderName()
	 */
	public function setCardHolderName($cardHolderName)
	{
		$this->_cardHolderName = $cardHolderName;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getPaymentAccountUniqueId()
	 */
	public function getPaymentAccountUniqueId()
	{
		return $this->_paymentAccountUniqueId;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setPaymentAccountUniqueId()
	 */
	public function setPaymentAccountUniqueId($paymentAccountUniqueId)
	{
		$this->_paymentAccountUniqueId = $paymentAccountUniqueId;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getIsToken()
	 */
	public function getIsToken()
	{
		return $this->_isToken;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setIsToken()
	 */
	public function setIsToken($isToken)
	{
		$this->_isToken = $isToken;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getPaymentAccountBin()
	 */
	public function getPaymentAccountBin()
	{
		return $this->_paymentAccountBin;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setPaymentAccountBin()
	 */
	public function setPaymentAccountBin($paymentAccountBin)
	{
		$this->_paymentAccountBin = $paymentAccountBin;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getExpireDate()
	 */
	public function getExpireDate()
	{
		return $this->_expireDate;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setExpireDate()
	 */
	public function setExpireDate($expireDate)
	{
		$this->_expireDate = $expireDate;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::getCardType()
	 */
	public function getCardType()
	{
		return $this->_cardType;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payment_ICard::setCardType()
	 */
	public function setCardType($cardType)
	{
		$this->_cardType = $cardType;
		return $this;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payload::_canSerialize()
	 */
	protected function _canSerialize()
	{
		return (trim($this->_serializeContents()) !== '');
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payload::_getRootNodeName()
	 */
	protected function _getRootNodeName()
	{
		return static::ROOT_NODE;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payload::_getXmlNamespace()
	 */
	protected function _getXmlNamespace()
	{
		return self::XML_NS;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Payload::_serializeContents()
	 */
	protected function _serializeContents()
	{
		return $this->_serializeOptionalValue('CardHolderName', $this->getCardHolderName())
			. $this->_serializePaymentAccountUniqueId()
			. $this->_serializeOptionalValue('ExpireDate', $this->getExpireDate())
			. $this->_serializeOptionalValue('CardType', $this->getCardType());
	}

	/**
	 * Serialize the payment account unique id node if there's a valid value.
	 *
	 * @return string
	 */
	protected function _serializePaymentAccountUniqueId()
	{
		$isToken = $this->getIsToken();
		$isToken = !is_null($isToken) ? $isToken : false;
		$paymentAccountUniqueId = $this->getPaymentAccountUniqueId();
		return $paymentAccountUniqueId ? "<PaymentAccountUniqueId isToken=\"{$isToken}\">{$paymentAccountUniqueId}</PaymentAccountUniqueId>" : '';
	}
}

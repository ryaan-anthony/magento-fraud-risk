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

class EbayEnterprise_RiskService_Sdk_Line_Details
	extends EbayEnterprise_RiskService_Sdk_Iterable
	implements EbayEnterprise_RiskService_Sdk_Line_IDetails
{
	/**
	 * Get an empty instance of the line detail payload
	 *
	 * @return EbayEnterprise_RiskService_Sdk_Line_IDetail
	 */
	public function getEmptyLineDetail()
	{
		return $this->_buildPayloadForModel(static::LINE_DETAIL_MODEL);
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Iterable::_getNewSubpayload()
	 */
	protected function _getNewSubpayload()
	{
		return $this->getEmptyLineDetail();
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Iterable::_getSubpayloadXPath()
	 */
	protected function _getSubpayloadXPath()
	{
		return 'x:' . static::SUBPAYLOAD_XPATH;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Iterable::_getRootNodeName()
	 */
	protected function _getRootNodeName()
	{
		return static::ROOT_NODE;
	}

	/**
	 * @see EbayEnterprise_RiskService_Sdk_Iterable::_getXmlNamespace()
	 */
	protected function _getXmlNamespace()
	{
		return self::XML_NS;
	}
}

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

interface EbayEnterprise_RiskService_Sdk_IRequest extends EbayEnterprise_RiskService_Sdk_Payload_ITop
{
	const ROOT_NODE = 'RiskAssessmentRequest';
	const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
	const XSD = 'Risk-Service-RiskAssessment-1.0.xsd';
	const ORDER_MODEL ='EbayEnterprise_RiskService_Sdk_Order';
    const SERVER_INFO_MODEL = 'EbayEnterprise_RiskService_Sdk_Server_Info';
    const DEVICE_INFO_MODEL = 'EbayEnterprise_RiskService_Sdk_Device_Info';

	/**
	 * Contain order detail information.
	 *
	 * @return EbayEnterprise_RiskService_Sdk_IOrder
	 */
	public function getOrder();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_IOrder
	 * @return self
	 */
	public function setOrder(EbayEnterprise_RiskService_Sdk_IOrder $order);

    /**
     * @return EbayEnterprise_RiskService_Sdk_Device_IInfo
     */
    public function getDeviceInfo();

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Device_IInfo
     * @return self
     */
    public function setDeviceInfo(EbayEnterprise_RiskService_Sdk_Device_IInfo $deviceInfo);

    /**
     * @return EbayEnterprise_RiskService_Sdk_Server_IInfo
     */
    public function getServerInfo();

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Server_IInfo
     * @return self
     */
    public function setServerInfo(EbayEnterprise_RiskService_Sdk_Server_IInfo $serverInfo);
}

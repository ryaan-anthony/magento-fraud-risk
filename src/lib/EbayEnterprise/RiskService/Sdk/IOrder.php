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

interface EbayEnterprise_RiskService_Sdk_IOrder extends EbayEnterprise_RiskService_Sdk_IPayload
{
	const ROOT_NODE = 'Order';
	const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
	const SHIPPING_LIST_MODEL ='EbayEnterprise_RiskService_Sdk_Shipping_List';
	const LINE_ITEMS_MODEL ='EbayEnterprise_RiskService_Sdk_Line_Items';
	const PAYMENTS_MODEL ='EbayEnterprise_RiskService_Sdk_Payments';
	const TOTAL_MODEL ='EbayEnterprise_RiskService_Sdk_Total';
    const SHOPPING_SESSION_MODEL = 'EbayEnterprise_RiskService_Sdk_ShoppingSession';
    const CUSTOMER_LIST_MODEL = 'EbayEnterprise_RiskService_Sdk_Customer_List';

	/**
	 * Unique identifier of the order in the web site.
	 *
	 * xsd restrictions: 1-40 characters
	 * @return string
	 */
	public function getOrderId();

	/**
	 * @param  string
	 * @return self
	 */
	public function setOrderId($orderId);

    /**
     * Unique identifier of the order in the web site.
     *
     * xsd restrictions: 1-40 characters
     * @return string
     */
    public function getPromoCode();

    /**
     * @param  string
     * @return self
     */
    public function setPromoCode($promoCode);

    /**
     * @return EbayEnterprise_RiskService_Sdk_Customer_IList
     */
    public function getCustomerList();

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Customer_IList
     * @return self
     */
    public function setCustomerList(EbayEnterprise_RiskService_Sdk_Customer_IList $customerList);

	/**
	 * @return EbayEnterprise_RiskService_Sdk_Shipping_IList
	 */
	public function getShippingList();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_Shipping_IList
	 * @return self
	 */
	public function setShippingList(EbayEnterprise_RiskService_Sdk_Shipping_IList $shippingList);

	/**
	 * @return EbayEnterprise_RiskService_Sdk_Line_IItems
	 */
	public function getLineItems();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_Line_IItems
	 * @return self
	 */
	public function setLineItems(EbayEnterprise_RiskService_Sdk_Line_IItems $lineItems);

    /**
     * @return EbayEnterprise_RiskService_Sdk_IShoppingSession
     */
    public function getShoppingSession();

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IShoppingSession
     * @return self
     */
    public function setShoppingSession(EbayEnterprise_RiskService_Sdk_IShoppingSession $shoppingSession);

	/**
	 * @return EbayEnterprise_RiskService_Sdk_ITotal
	 */
	public function getTotalCost();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_ITotal
	 * @return self
	 */
	public function setTotalCost(EbayEnterprise_RiskService_Sdk_ITotal $totalCost);
}

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

class EbayEnterprise_Eb2cFraud_Model_Build_Request
    extends EbayEnterprise_Eb2cFraud_Model_Abstract
    implements EbayEnterprise_Eb2cFraud_Model_Build_IRequest
{
    /** @var EbayEnterprise_RiskService_Sdk_IPayload */
    protected $_request;
    /** @var EbayEnterprise_Eb2cFraud_Model_Risk_Service */
    protected $_service;
    /** @var Mage_Sales_Model_Order */
    protected $_order;
    /** @var Mage_Sales_Model_Quote */
    protected $_quote;
    /** @var EbayEnterprise_Eb2cFraud_Helper_Data */
    protected $_helper;
    /** @var EbayEnterprise_Eb2cFraud_Helper_Http */
    protected $_httpHelper;
    /** @var EbayEnterprise_Eb2cFraud_Helper_Config */
    protected $_config;
    /** @var Mage_Catalog_Model_Product */
    protected $_product;
    /** @var string */
    protected $_shippingId;
    /** @var string */
    protected $_billingId;

    /**
     * @param array $initParams Must have this key:
     *                          - 'request' => EbayEnterprise_RiskService_Sdk_IPayload
     *                          - 'order' => Mage_Sales_Model_Order
     *                          - 'quote' => Mage_Sales_Model_Quote
     *                          - 'helper' => EbayEnterprise_Eb2cFraud_Helper_Data
     *                          - 'product' => Mage_Catalog_Model_Product
     *				- 'config'  => EbayEnterprise_Eb2cFraud_Helper_config
     */
    public function __construct(array $initParams=array())
    {
        list($this->_request, $this->_order, $this->_quote, $this->_helper, $this->_httpHelper, $this->_product, $this->_config, $this->_service) = $this->_checkTypes(
            $this->_nullCoalesce($initParams, 'request', $this->_getNewSdkInstance('EbayEnterprise_RiskService_Sdk_Request')),
            $this->_nullCoalesce($initParams, 'order', $initParams['order']),
            $this->_nullCoalesce($initParams, 'quote', Mage::getModel('sales/quote')),
            $this->_nullCoalesce($initParams, 'helper', Mage::helper('eb2cfraud')),
            $this->_nullCoalesce($initParams, 'http_helper', Mage::helper('eb2cfraud/http')),
            $this->_nullCoalesce($initParams, 'product', Mage::getModel('catalog/product')),
	    $this->_nullCoalesce($initParams, 'config', Mage::helper('eb2cfraud/config')),
	    $this->_nullCoalesce($initParams, 'service', Mage::getModel('eb2cfraud/risk_service'))
        );
    }

    /**
     * Type hinting for self::__construct $initParams
     *
     * @param  EbayEnterprise_RiskService_Sdk_IPayload
     * @param  Mage_Sales_Model_Order
     * @param  Mage_Sales_Model_Quote
     * @param  EbayEnterprise_Eb2cFraud_Helper_Data
     * @param  EbayEnterprise_Eb2cFraud_Helper_Http
     * @param  Mage_Catalog_Model_Product
     * @param  EbayEnterprise_Eb2cFraud_Helper_Config
     * @param  EbayEnterprise_Eb2cFraud_Model_Risk_Service
     * @return array
     */
    protected function _checkTypes(
        EbayEnterprise_RiskService_Sdk_IPayload $request,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Quote $quote,
        EbayEnterprise_Eb2cFraud_Helper_Data $helper,
        EbayEnterprise_Eb2cFraud_Helper_Http $httpHelper,
        Mage_Catalog_Model_Product $product,
	EbayEnterprise_Eb2cFraud_Helper_Config $config,
	EbayEnterprise_Eb2cFraud_Model_Risk_Service $service
    ) {
        return array($request, $order, $quote, $helper, $httpHelper, $product, $config, $service);
    }

    public function build()
    {
        $this->_buildRequest();
        return $this->_request;
    }

    /**
     * @return string | null
     */
    protected function _getPaymentTransactionDate()
    {
        $quote = $this->_quote->loadByIdWithoutStore($this->_order->getQuoteId());
        return $quote->getId() ? $this->_getPaymentCreatedDate($quote) : null;
    }

    /**
     * @param  Mage_Sales_Model_Quote
     * @return string | null
     */
    protected function _getPaymentCreatedDate(Mage_Sales_Model_Quote $quote)
    {
        $payment = $quote->getPayment();
        return $payment ? $payment->getCreatedAt() : null;
    }

    /**
     * @return array
     */
    protected function _getHttpHeaders()
    {
        $headers = $this->_service->getHttpHeaders();
        return $headers ? json_decode($headers, true) : array();
    }

    /**
     * @return string | null
     */
    protected function _getShippingId()
    {
        if (!$this->_shippingId) {
            $shippingAddress = $this->_order->getShippingAddress();
            $this->_shippingId = $shippingAddress ? $shippingAddress->getId() : null;
        }
        return $this->_shippingId;
    }

    /**
     * @return string | null
     */
    protected function _getBillingId()
    {
        if (!$this->_billingId) {
            $this->_billingId = $this->_order->getBillingAddress()->getId();
        }
        return $this->_billingId;
    }

    /**
     * @param  Mage_Core_Model_Abstract
     * @return string | null
     */
    protected function _getItemCategory(Mage_Core_Model_Abstract $item)
    {
        $product = $this->_product->load($item->getProductId());
        return $product->getId() ? $this->_getCategoryName($product) : null;
    }

    /**
     * Get category collection.
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoryCollection()
    {
        return Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name');
    }

    /**
     * @param  Mage_Core_Model_Abstract
     * @return string | null
     */
    protected function _getCategoryName(Mage_Core_Model_Abstract $product)
    {
        $categoryName = '';
        $categories = $product->getCategoryCollection();
        $collection = $this->_getCategoryCollection();
        foreach ($categories as $category) {
            $pathArr = explode('/', $category->getPath());
            array_walk($pathArr, function(&$val) use ($collection) {
                $part = $collection->getItemById((int) $val);
                $val = $part ? $part->getName() : null;
            });
            $catString = implode('->', array_filter($pathArr));
            if ($catString) {
                $categoryName .= $this->_getCategoryDelimiter($categoryName) . $catString;
            }
        }
        return $categoryName;
    }

    /**
     * @param string
     */
    protected function _getCategoryDelimiter($categoryName)
    {
        return $categoryName ? ',' : '';
    }

    /**
     * @return self
     */
    protected function _buildRequest()
    {
        $this->_buildOrder($this->_request->getOrder());
	$this->_buildServerInfo($this->_request->getServerInfo());
        $this->_buildDeviceInfo($this->_request->getDeviceInfo());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IOrder
     * @return self
     */
    protected function _buildOrder(EbayEnterprise_RiskService_Sdk_IOrder $subPayloadOrder)
    {
        $subPayloadOrder->setOrderId($this->_order->getIncrementId());
	$subPayloadOrder->setPromoCode($this->_order->getCouponCode());	

	$this->_buildCustomerList($subPayloadOrder->getCustomerList())
             ->_buildShippingList($subPayloadOrder->getShippingList())
             ->_buildLineItems($subPayloadOrder->getLineItems())
	     ->_buildShoppingSession($subPayloadOrder->getShoppingSession())
             ->_buildTotalCost($subPayloadOrder->getTotalCost());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Customer_IList
     * @return self
     */
    protected function _buildCustomerList(EbayEnterprise_RiskService_Sdk_Customer_IList $subPayloadCustomerList)
    {
	$customerID = $this->_order->getCustomerId();
	$customerData = Mage::getModel('customer/customer')->load($customerID); // then load customer by customer id

        $subPayloadCustomer = $subPayloadCustomerList->getEmptyCustomer();
        $this->_buildCustomer($subPayloadCustomer, $customerData);
        $subPayloadCustomerList->offsetSet($subPayloadCustomer);
        
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Shipping_IList
     * @return self
     */
    protected function _buildShippingList(EbayEnterprise_RiskService_Sdk_Shipping_IList $subPayloadShippingList)
    {
        $shipments = $this->_getOrderShippingData();
        foreach ($shipments as $shipment) {
            $subPayloadShipment = $subPayloadShippingList->getEmptyShipment();
            $this->_buildShipment($subPayloadShipment, $shipment['address'], $shipment['type']);
            $subPayloadShippingList->offsetSet($subPayloadShipment);
        }
        return $this;
    }

    /**
     * When the order is virtual simply return virtual shipment data otherwise
     * find out if the order has any items that are virtual to return a combination
     * of both virtual and physical shipment data. However, if the order only
     * has physical items simply return physical shipment data.
     *
     * @return array
     */
    protected function _getOrderShippingData()
    {
        return $this->_order->getIsVirtual()
            ? $this->_getVirtualOrderShippingData()
            : $this->_getPhysicalVirtualShippingData();
    }

    /**
     * Determine if the order has an virtual items, if so,
     * simply return a combination of physical and virtual shipment
     * data. Otherwise, simply return physical shipment data.
     *
     * @return array
     */
    protected function _getPhysicalVirtualShippingData()
    {
        return $this->_hasVirtualItems()
            ? array_merge($this->_getPhysicalOrderShippingData(), $this->_getVirtualOrderShippingData())
            : $this->_getPhysicalOrderShippingData();
    }

    /**
     * Returns virtual shipment data.
     *
     * @return array
     */
    protected function _getVirtualOrderShippingData()
    {
        return array(array(
            'type' => static::VIRTUAL_SHIPMENT_TYPE,
            'address' => $this->_order->getBillingAddress(),
        ));
    }

    /**
     * Returns physical shipment data.
     *
     * @return array
     */
    protected function _getPhysicalOrderShippingData()
    {
        return array(array(
            'type' => static::PHYSICAL_SHIPMENT_TYPE,
            'address' => $this->_order->getShippingAddress(),
        ));
    }

    /**
     * Returns true when the item is virtual otherwise false.
     *
     * @param  Mage_Sales_Model_Order_Item
     * @return bool
     */
    protected function _isItemVirtual(Mage_Sales_Model_Order_Item $item)
    {
        return ((int) $item->getIsVirtual() === 1);
    }

    /**
     * Returns true when the passed in type is a physical shipment type
     * otherwise false.
     *
     * @param  string
     * @return bool
     */
    protected function _isVirtualShipmentType($type)
    {
        return ($type !== static::PHYSICAL_SHIPMENT_TYPE);
    }

    /**
     * Returns true if any items in the order is virtual, otherwise,
     * return false.
     *
     * @return bool
     */
    protected function _hasVirtualItems()
    {
        $hasVirtual = false;
        foreach ($this->_order->getAllItems() as $orderItem) {
            if ($this->_isItemVirtual($orderItem)) {
                $hasVirtual = true;
                break;
            }
        }
        return $hasVirtual;
    }

    /**
     * Returns the billing id if the item is virtual otherwise returns
     * the shipping id.
     *
     * @param  Mage_Sales_Model_Order_Item
     * @return string
     */
    protected function _getShipmentIdByItem(Mage_Sales_Model_Order_Item $item)
    {
        return $this->_isItemVirtual($item) ? $this->_getBillingId() : $this->_getShippingId();
    }

    /**
     * Returns the virtual shipping method when the types is a virtual shipment
     * otherwise returns the shipping method in the order.
     *
     * @param  string
     * @return string
     */
    protected function _getShippingMethodByType($type)
    {
        return $this->_isVirtualShipmentType($type)
            ? static::VIRTUAL_SHIPPING_METHOD
            : $this->_order->getShippingMethod();
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Line_IItems
     * @return self
     */
    protected function _buildLineItems(EbayEnterprise_RiskService_Sdk_Line_IItems $subPayloadLineItems)
    {
        foreach ($this->_order->getAllItems() as $orderItem) {
            $subPayloadLineItem = $subPayloadLineItems->getEmptyLineItem();
            $this->_buildLineItem($subPayloadLineItem, $orderItem);
            $subPayloadLineItems->offsetSet($subPayloadLineItem);
        }
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_ITotal
     * @return self
     */
    protected function _buildTotalCost(EbayEnterprise_RiskService_Sdk_ITotal $subPayloadTotalCost)
    {
        $subPayloadCostTotals = $subPayloadTotalCost->getCostTotals();
        $subPayloadCostTotals->setAmountBeforeTax($this->_order->getSubtotal())
            ->setAmountAfterTax($this->_order->getGrandTotal())
	    ->setCurrencyCode($this->_order->getBaseCurrencyCode());
        $subPayloadTotalCost->setCostTotals($subPayloadCostTotals);
       
	$failedCc = Mage::getSingleton('core/session')->getCCAttempts() - 1;

        if($failedCc < 0 )
        {
                $failedCc = 0;
        }

        $subPayloadTotalCost->setFailedCc($failedCc);
 
	$orderBillingAddress = $this->_order->getBillingAddress();
        $orderPayment = $this->_order->getPayment();
        if ($orderBillingAddress && $orderPayment) {
            $this->_buildPayment($subPayloadTotalCost->getFormOfPayment(), $orderBillingAddress, $orderPayment);
        }

	return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Device_IInfo
     * @return self
     */
    protected function _buildDeviceInfo(EbayEnterprise_RiskService_Sdk_Device_IInfo $subPayloadDeviceInfo)
    {
	$sessionId = Mage::getSingleton('core/session')->getEncryptedSessionId();

	$subPayloadDeviceInfo->setJSCData($this->_httpHelper->getJavaScriptFraudData());
	$subPayloadDeviceInfo->setSessionID($sessionId);
	$subPayloadDeviceInfo->setDeviceIP($this->getNewRemoteAddr());
	$subPayloadDeviceInfo->setDeviceHostname(gethostbyaddr($this->getNewRemoteAddr()));
	$this->_buildHttpHeaders($subPayloadDeviceInfo->getHttpHeaders());
	$subPayloadDeviceInfo->setUserCookie($this->_httpHelper->getCookiesString());

        return $this;
    }

    private function getNewRemoteAddr()
    {
	$remoteAddr = Mage::helper('core/http')->getRemoteAddr();

	if( $this->ip_is_private($remoteAddr))
	{
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        	{
        		$ip_array=array_values(array_filter(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR'])));
                	$remoteAddr = $ip_array[0];

                	if(!$remoteAddr)
                	{
                		$remoteAddr = Mage::helper('core/http')->getRemoteAddr();
                	}
         	} else {
         		$remoteAddr = Mage::helper('core/http')->getRemoteAddr();
         	}

	 	return $remoteAddr;
    	}
    }

    private function ip_is_private($ip)
    {
        $privateAddresses = [ '10.0.0.0|10.255.255.255', '172.16.0.0|172.31.255.255', '192.168.0.0|192.168.255.255', '169.254.0.0|169.254.255.255', '127.0.0.0|127.255.255.255' ];

        $long_ip = ip2long($ip);
        if($long_ip != -1) 
	{
            foreach($privateAddresses as $pri_addr)
            {
                list($start, $end) = explode('|', $pri_addr);

                // IF IS PRIVATE
                if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
		{
                	return true;
		}
            }
    	}

	return false;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Server_IInfo
     * @return self
     */
    protected function _buildServerInfo(EbayEnterprise_RiskService_Sdk_Server_IInfo $subPayloadServerInfo)
    {
	$createdAt = $this->_order->getPayment()->getCreatedAt();

	// SERVER INFO - TYPE
	$subPayloadServerInfo->setTime(date("Y-m-d\TH:i:s.000\Z", strtotime($this->_getPaymentTransactionDate())));

	$storeTimeZone = Mage::getStoreConfig('general/locale/timezone');
	$offset = (timezone_offset_get(new DateTimeZone($storeTimeZone), new DateTime()) / 60) / 60;

	$subPayloadServerInfo->setTZOffset($offset);

	if( strtotime($this->_order->getCreatedAtStoreDate()) != time() )
	{
		$subPayloadServerInfo->setTZOffsetRaw($offset);
	}

	date_default_timezone_set($storeTimeZone);
	$bool = date('I'); // this will be 1 in DST or else 0

	if( $bool )
	{
		$subPayloadServerInfo->setDSTActive("true");
	} else {
		$subPayloadServerInfo->setDSTActive("false");
	}

        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IShipment
     * @param  Mage_Customer_Model_Address_Abstract
     * @param  string
     * @return self
     */
    protected function _buildShipment(
        EbayEnterprise_RiskService_Sdk_IShipment $subPayloadShipment,
        Mage_Customer_Model_Address_Abstract $orderShippingAddress,
	$type
    )
    {
	$subPayloadShipment->setAddressId($orderShippingAddress->getId())
        		   ->setShipmentId($orderShippingAddress->getId());

	$subPayloadCostTotals = $subPayloadShipment->getCostTotals();
        $subPayloadCostTotals->setAmountBeforeTax($this->_order->getSubtotal())
            ->setAmountAfterTax($this->_order->getGrandTotal())
	    ->setCurrencyCode($this->_order->getBaseCurrencyCode());
        $subPayloadShipment->setCostTotals($subPayloadCostTotals);

	$subPayloadShipment->setShippingMethod($this->_getShippingMethodByType($type));
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_ICustomer
     * @param  Mage_Customer_Model_Customer
     * @param  string
     * @return self
     */
    protected function _buildCustomer(
        EbayEnterprise_RiskService_Sdk_ICustomer $subPayloadCustomer,
        Mage_Customer_Model_Customer $orderCustomer
    )
    {
	$this->_buildPersonName($subPayloadCustomer->getPersonName(), $this->_order->getBillingAddress()); 
	$subPayloadCustomer->setEmail($this->_order->getCustomerEmail());

	$this->_buildTelephone($subPayloadCustomer->getTelephone(), $this->_order->getBillingAddress());
        $this->_buildAddress($subPayloadCustomer->getAddress(), $this->_order->getShippingAddress());

	// MemberLoggedIn
	$sessionCustomer = Mage::getSingleton("customer/session");
	if($sessionCustomer->isLoggedIn()) {
		$subPayloadCustomer->setMemberLoggedIn("true");
	} else {
		$subPayloadCustomer->setMemberLoggedIn("false");
	}

	$subPayloadCustomer->setCurrencyCode($this->_order->getBaseCurrencyCode());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Person_IName
     * @param  Mage_Customer_Model_Address_Abstract
     * @return self
     */
    protected function _buildPersonName(
        EbayEnterprise_RiskService_Sdk_Person_IName $subPayloadPersonName,
        Mage_Customer_Model_Address_Abstract $orderAddress
    )
    {
        $subPayloadPersonName->setLastName($orderAddress->getLastname())
            ->setMiddleName($orderAddress->getMiddlename())
            ->setFirstName($orderAddress->getFirstname());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_ITelephone
     * @param  Mage_Customer_Model_Address_Abstract
     * @return self
     */
    protected function _buildTelephone(
        EbayEnterprise_RiskService_Sdk_ITelephone $subPayloadTelephone,
        Mage_Customer_Model_Address_Abstract $orderAddress
    )
    {
        $subPayloadTelephone->setCountryCode(null)
            ->setAreaCode(null)
            ->setNumber($orderAddress->getTelephone())
            ->setExtension(null);
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IAddress
     * @param  Mage_Customer_Model_Address_Abstract
     * @return self
     */
    protected function _buildAddress(
        EbayEnterprise_RiskService_Sdk_IAddress $subPayloadAddress,
        Mage_Customer_Model_Address_Abstract $orderAddress
    )
    {
        $subPayloadAddress->setLineA($orderAddress->getStreet(1))
            ->setLineB($orderAddress->getStreet(2))
            ->setLineC($orderAddress->getStreet(3))
            ->setLineD($orderAddress->getStreet(4))
            ->setCity($orderAddress->getCity())
            ->setPostalCode($orderAddress->getPostcode())
            ->setMainDivision($orderAddress->getRegionCode())
            ->setCountryCode($orderAddress->getCountryId())
	    ->setAddressID($orderAddress->getId());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Line_IItem
     * @param  Mage_Core_Model_Abstract
     * @return self
     */
    protected function _buildLineItem(
        EbayEnterprise_RiskService_Sdk_Line_IItem $subPayloadLineItem,
        Mage_Core_Model_Abstract $orderItem
    )
    {
	$lineItemAmount = (int)$orderItem->getQtyOrdered() * $orderItem->getPrice();

	$subPayloadLineItem->setLineTotalAmount($lineItemAmount)
			   ->setUnitCost($orderItem->getPrice())
			   ->setQuantity((int) $orderItem->getQtyOrdered())
			   ->setProductName($orderItem->getName())
			   ->setDescription($orderItem->getName())
			   ->setUnitWeight($orderItem->getWeight())
			   ->setUnitOfMeasure($this->_config->getUnitOfMeasure())
			   ->setUnitCurrencyCode($this->_order->getBaseCurrencyCode())
			   ->setCategory($this->_getItemCategory($orderItem))
			   ->setPromoCode($this->_order->getCouponCode())
			   ->setProductId($orderItem->getSku())
			   ->setLineItemId($orderItem->getId())
			   ->setShipmentId($this->_getShipmentIdByItem($orderItem));

        return $this;
    }

    /**
     * @return EbayEnterprise_RiskService_Model_Payment_IAdapter
     */
    protected function _getPaymentAdapter()
    {
        return Mage::getModel('eb2cfraud/payment_adapter', array(
            'order' => $this->_order
        ));
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Line_IItem
     * @param  Mage_Customer_Model_Address_Abstract
     * @param  Mage_Sales_Model_Order_Payment
     * @return self
     */
    protected function _buildPayment(
        EbayEnterprise_RiskService_Sdk_IPayment $subPayloadPayment,
        Mage_Customer_Model_Address_Abstract $orderBillingAddress,
        Mage_Sales_Model_Order_Payment $orderPayment
    )
    {
        $items = $this->_order->getAllItems();
        $itemcount= count($items);

        $paymentAdapterType = $this->_getPaymentAdapter()->getAdapter();
        $this->_buildPaymentCard($subPayloadPayment->getPaymentCard(), $paymentAdapterType)
	    ->_buildAuthorization($subPayloadPayment->getAuthorization())
            ->_buildPersonName($subPayloadPayment->getPersonName(), $orderBillingAddress)
            ->_buildTelephone($subPayloadPayment->getTelephone(), $orderBillingAddress)
            ->_buildAddress($subPayloadPayment->getAddress(), $orderBillingAddress)
            ->_buildTransactionResponses($subPayloadPayment->getTransactionResponses(), $paymentAdapterType);

	    $subPayloadPayment->setEmail($this->_order->getCustomerEmail())
            ->setPaymentTransactionDate($this->_helper->getNewDateTime($this->_getPaymentTransactionDate()))
            ->setCurrencyCode($this->_order->getBaseCurrencyCode())
            ->setAmount($orderPayment->getAmountAuthorized())
            ->setPaymentTransactionTypeCode($this->_config->getTenderTypeForCcType($orderPayment->getCcType()))
            ->setPaymentTransactionID($orderPayment->getId())
            ->setItemListRPH($itemcount);
        
        if( $orderPayment->getCcLast4())
        {
            $subPayloadPayment->setTenderClass("CreditCard");
        } else {
            $subPayloadPayment->setTenderClass("Other");
        }

        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Payment_ICard
     * @param  EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_IType
     * @return self
     */
    protected function _buildPaymentCard(
        EbayEnterprise_RiskService_Sdk_Payment_ICard $subPayloadCard,
        EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_IType $paymentAdapterType
    )
    {
        $subPayloadCard->setCardHolderName($paymentAdapterType->getExtractCardHolderName())
            ->setPaymentAccountUniqueId($paymentAdapterType->getExtractPaymentAccountUniqueId())
            ->setIsToken($paymentAdapterType->getExtractIsToken())
            ->setPaymentAccountBin($paymentAdapterType->getExtractPaymentAccountBin())
            ->setExpireDate($paymentAdapterType->getExtractExpireDate())
            ->setCardType($paymentAdapterType->getExtractCardType());
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Transaction_IResponses
     * @param  EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_IType
     * @return self
     */
    protected function _buildTransactionResponses(
        EbayEnterprise_RiskService_Sdk_Transaction_IResponses $subPayloadResponses,
        EbayEnterprise_Eb2cFraud_Model_Payment_Adapter_IType $paymentAdapterType
    )
    {
        $transactionResponses = (array) $paymentAdapterType->getExtractTransactionResponses();
        foreach ($transactionResponses as $transaction) {
            $subPayloadResponse = $subPayloadResponses->getEmptyTransactionResponse();
            $this->_buildTransactionResponse($subPayloadResponse, $transaction['response'], $transaction['type']);
            $subPayloadResponses->offsetSet($subPayloadResponse);
        }
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IAuthorization
     * @return self
     */
    protected function _buildAuthorization(
        EbayEnterprise_RiskService_Sdk_IAuthorization $subPayloadAuthorization
    )
    {
	$orderM = Mage::getModel('sales/order')->load($this->_order->getId());
        $payment = $orderM->getPayment();
        $paymentAdditional = $payment->getAdditionalInformation(); 

	if( !array_key_exists('response_code', $paymentAdditional) )
	{
		$subPayloadAuthorization->setDecline("true");
	} else {
		if( strcmp($paymentAdditional['response_code'], "APPROVED") === 0 )
        	{
			$subPayloadAuthorization->setDecline("false");
			$subPayloadAuthorization->setCode($paymentAdditional['bank_authorization_code']);
		} else {
			$subPayloadAuthorization->setDecline("true");
        	}
	}	 

        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_IShoppingSession
     * @return self
     */
    protected function _buildShoppingSession(
        EbayEnterprise_RiskService_Sdk_IShoppingSession $subPayloadShoppingSession
    )
    {
	// SHOPPING SESSION - TYPE
	$visitorData = Mage::getSingleton('core/session')->getVisitorData();
	$lastVisit = $visitorData['first_visit_at'];
	$newVisitor = $visitorData['is_new_visitor'];

	$lastVisitorInt = strtotime($lastVisit);
	$nowtime = time();
	$diff = $nowtime - $lastVisitorInt;
	$minutes = date('i', $diff);

	$subPayloadShoppingSession->setTimeOnSite($minutes);

	if( !$newVisitor )
	{
		$subPayloadShoppingSession->setReturnCustomer("true");
	} else {
		$subPayloadShoppingSession->setReturnCustomer("false");
	} 

	// ADD REMOVED ITEM FROM CART ATTRIBUTE	
	$removed = Mage::getSingleton('core/session')->getPrevItemQuoteRemoval();

	if( $removed )
	{
		$subPayloadShoppingSession->setItemsRemoved("true");
	} else {
		$subPayloadShoppingSession->setItemsRemoved("false");
	}
		
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Transaction_IResponse
     * @param  string
     * @param  string
     * @return self
     */
    protected function _buildTransactionResponse(
        EbayEnterprise_RiskService_Sdk_Transaction_IResponse $subPayloadResponse,
        $response,
        $type
    )
    {
        $subPayloadResponse->setResponse($response)
            ->setResponseType($type);
        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Http_IHeaders
     * @return self
     */
    protected function _buildHttpHeaders(EbayEnterprise_RiskService_Sdk_Http_IHeaders $subPayloadHttpHeaders)
    {
	 $httpHeaderZend = array(
                array( 'name' => 'host', 'message' => $this->_httpHelper->getHttpHost()),
                array( 'name' => 'origin', 'message' => $this->_httpHelper->getHttpOrigin()),
                array( 'name' => 'x-prototype-version', 'message' => $this->_httpHelper->getHttpXPrototypeVersion()),
                array( 'name' => 'x-requested-with', 'message' => $this->_httpHelper->getHttpXRequestedWith()),
                array( 'name' => 'user-agent', 'message' => $this->_httpHelper->getHttpUserAgent()),
                array( 'name' => 'accept', 'messsage' => $this->_httpHelper->getHttpAccept()),
                array( 'name' => 'accept-language', 'message' => $this->_httpHelper->getHttpAcceptLanguage()),
                array( 'name' => 'accept-encoding', 'message' => $this->_httpHelper->getHttpAcceptEncoding()),
                array( 'name' => 'cookie', 'message' => $this->_httpHelper->getCookiesString()),
                array( 'name' => 'x-forwarded-proto', 'message' => $this->_httpHelper->getHttpXForwardedProto()),
                array( 'name' => 'x-forwarded-for', 'message' => $this->_httpHelper->getHttpXForwardedFor()),
                array( 'name' => 'content-type', 'message' => $this->_httpHelper->getHttpContentType()),
                array( 'name' => 'connection', 'message' => $this->_httpHelper->getHttpConnection()),
                array( 'name' => 'accept-charset', 'message' => $this->_httpHelper->getHttpAcceptCharset()),
                array( 'name' => 'referrer', 'message' => $this->_httpHelper->getHttpReferrer())
        );
        foreach ($httpHeaderZend as $headerProperty) {
                if( isset($headerProperty['message']))
                {
                	$subPayloadHttpHeader = $subPayloadHttpHeaders->getEmptyHttpHeader();
                        $this->_buildHttpHeader($subPayloadHttpHeader, $headerProperty['name'], $headerProperty['message']);
                        $subPayloadHttpHeaders->offsetSet($subPayloadHttpHeader);
                }
        }

        return $this;
    }

    /**
     * @param  EbayEnterprise_RiskService_Sdk_Http_IHeader
     * @param  string
     * @param  string
     * @return self
     */
    protected function _buildHttpHeader(EbayEnterprise_RiskService_Sdk_Http_IHeader $subPayloadHttpHeader, $name, $message)
    {
        $subPayloadHttpHeader->setHeader($message)
            ->setName($name);
        return $this;
    }
}

<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\FrontOrderData;
use SS6\ShopBundle\Model\Order\OrderData;

class OrderDataMapper {

	/**
	 * @param \SS6\ShopBundle\Model\Order\FrontOrderData $frontOrderData
	 * @return \SS6\ShopBundle\Model\Order\OrderData
	 */
	public function getOrderDataFromFrontOrderData(FrontOrderData $frontOrderData) {
		$orderData = new OrderData();
		$orderData->transport = $frontOrderData->transport;
		$orderData->payment = $frontOrderData->payment;
		$orderData->orderNumber = $frontOrderData->orderNumber;
		$orderData->status = $frontOrderData->status;
		$orderData->firstName = $frontOrderData->firstName;
		$orderData->lastName = $frontOrderData->lastName;
		$orderData->email = $frontOrderData->email;
		$orderData->telephone = $frontOrderData->telephone;
		$orderData->street = $frontOrderData->street;
		$orderData->city = $frontOrderData->city;
		$orderData->postcode = $frontOrderData->postcode;
		$orderData->deliveryAddressSameAsBillingAddress = $frontOrderData->deliveryAddressSameAsBillingAddress;
		$orderData->deliveryContactPerson = $frontOrderData->deliveryContactPerson;
		$orderData->deliveryCompanyName = $frontOrderData->deliveryCompanyName;
		$orderData->deliveryTelephone = $frontOrderData->deliveryTelephone;
		$orderData->deliveryStreet = $frontOrderData->deliveryStreet;
		$orderData->deliveryCity = $frontOrderData->deliveryCity;
		$orderData->deliveryPostcode = $frontOrderData->deliveryPostcode;
		$orderData->note = $frontOrderData->note;
		$orderData->itemsWithoutTransportAndPayment = $frontOrderData->itemsWithoutTransportAndPayment;
		$orderData->orderTransport = $frontOrderData->orderTransport;
		$orderData->orderPayment = $frontOrderData->orderPayment;
		$orderData->domainId = $frontOrderData->domainId;
		$orderData->currency = $frontOrderData->currency;

		if ($frontOrderData->companyCustomer) {
			$orderData->companyName = $frontOrderData->companyName;
			$orderData->companyNumber = $frontOrderData->companyNumber;
			$orderData->companyTaxNumber = $frontOrderData->companyTaxNumber;
		} else {
			$orderData->companyName = null;
			$orderData->companyNumber = null;
			$orderData->companyTaxNumber = null;
		}

		return $orderData;
	}

}

<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Setor7\Cielo\Gateway\Request\CustomerDataBuilder;
use Setor7\Cielo\Gateway\Request\SaleDataBuilder;

/**
 * Class AddressDataBuilder
 */
class AddressDataBuilder implements BuilderInterface
{
    /**
     * ShippingAddress block name
     */
    const SHIPPING_ADDRESS = 'DeliveryAddress';

    /**
     * BillingAddress block name
     */
    const BILLING_ADDRESS = 'Address';

    /**
     * The street address. Maximum 255 characters, and must contain at least 1 digit.
     * Required when AVS rules are configured to require street address.
     */
    const STREET_ADDRESS = 'Street';

    /**
     * The extended address information—such as apartment or suite number. 255 character maximum.
     */
    const STREET_NUMBER = 'Number';

    const COMPLEMENT = 'Complement';

    const ZIPCODE = 'ZipCode';

    /**
     * The locality/city. 255 character maximum.
     */
    const CITY = 'City';

    /**
     * The state or province. For PayPal addresses, the region must be a 2-letter abbreviation;
     * for all other payment methods, it must be less than or equal to 255 characters.
     */
    const STATE = 'State';

    /**
     * The ISO 3166-1 alpha-2 country code specified in an address.
     * The gateway only accepts specific alpha-2 values.
     *
     * @link https://developers.braintreepayments.com/reference/general/countries/php#list-of-countries
     */
    const COUNTRY_CODE = 'Country';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $result = [];

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress) {
            $result[SaleDataBuilder::SALE] = [
              CustomerDataBuilder::CUSTOMER => [
                self::BILLING_ADDRESS => [
                    self::STREET_ADDRESS => $billingAddress->getStreetLine1(),
                    self::STREET_NUMBER => $billingAddress->getStreetLine2(),
                    self::COMPLEMENT => '',
                    self::ZIPCODE => $billingAddress->getPostcode(),
                    self::CITY => $billingAddress->getCity(),
                    self::STATE => $billingAddress->getRegionCode(),
                    self::COUNTRY_CODE => $billingAddress->getCountryId()
                ]
            ]
          ];
        }

        $shippingAddress = $order->getShippingAddress();

        //TODO: verificar porque não tem shippingAddress, eu acredito que porque
        // o cliente ja tem um endereço cadastrado como default e ele não e mais
        // solicitado no checkout
        if (!$shippingAddress) {
          $shippingAddress = $billingAddress;
        }
        if ($shippingAddress) {
            $result[SaleDataBuilder::SALE] = [
              CustomerDataBuilder::CUSTOMER => [
                self::SHIPPING_ADDRESS => [
                  self::STREET_ADDRESS => $shippingAddress->getStreetLine1(),
                  self::STREET_NUMBER => $shippingAddress->getStreetLine2(),
                  self::COMPLEMENT => '',
                  self::ZIPCODE => $shippingAddress->getPostcode(),
                  self::CITY => $shippingAddress->getCity(),
                  self::STATE => $shippingAddress->getRegionCode(),
                  self::COUNTRY_CODE => $shippingAddress->getCountryId()
                ]
              ]
            ];
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info(" Build address ");
        return $result;
    }
}

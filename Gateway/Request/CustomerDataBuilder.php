<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Setor7\Cielo\Gateway\Request\SaleDataBuilder;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * Customer block name
     */
    const CUSTOMER = 'Customer';

    const NAME = 'Name';

    const EMAIL = 'Email';

    const BIRTHDATE = 'Birthdate';

    const IDENTITY = 'Identity';

    const IDENTITY_TYPE = 'IdentityType';
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
        $billingAddress = $order->getBillingAddress();

        $result = [];

        $result[SaleDataBuilder::SALE] = [
          self::CUSTOMER => [
                  self::NAME => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
                  self::EMAIL => $billingAddress->getEmail()
                  //TODO
                  //self::BIRTHDATE => substr($order->getCustomerDob(), 0, 9),
                  //self::IDENTITY => $order->getCustomerTaxvat(),
                  //self::IDENTITY_TYPE => 'CPF'
              ]
        ];
        return $result;
    }
}

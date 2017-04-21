<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class SaleDataBuilder implements BuilderInterface
{

    const SALE = 'Sale';

    const MERCHANT_ORDER_ID = 'MerchantOrderId';

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

        $result[self::SALE] = [
          self::MERCHANT_ORDER_ID => $order->getOrderIncrementId()
        ];

        return $result;
    }
}

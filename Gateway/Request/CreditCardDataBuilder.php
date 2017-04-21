<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Setor7\Cielo\Gateway\Request\SaleDataBuilder;
use Setor7\Cielo\Gateway\Request\AbstractPaymentDataBuilder;
use Setor7\Cielo\Model\Adminhtml\Source\Cctype;

/**
 * Class AddressDataBuilder
 */
class CreditCardDataBuilder implements BuilderInterface
{
    /**
     * ShippingAddress block name
     */
    const CREDIT_CARD = 'CreditCard';
    const CARDNUMBER = 'CardNumber';
    const HOLDER = 'Holder';
    const EXPIRATIONDATE = 'ExpirationDate';
    const SECURITYCODE = 'SecurityCode';
    const SAVECARD = 'SaveCard';
    const BRAND = 'Brand';
    const CARDTOKEN = 'CardToken';


    /**
     *
     */
    private $cctype;


    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader, Cctype $cctype)
    {
        $this->subjectReader = $subjectReader;
        $this->cctype = $cctype;

    }

    public function getBrandCard(string $card_code){
        $mapper_card = $this->cctype->toOptionArray();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(" Cards ==> " . json_encode($mapper_card));
        $result = null;

        foreach($mapper_card as $brands) {
            if ($card_code == $brands['value']) {
                $result = $brands['label'];
                break;
            }
        }
        //TODO
        return $result;
        //return "Elo";
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();
        $result = [];
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(" Build CreditCard ");

        $result[SaleDataBuilder::SALE] = [
          AbstractPaymentDataBuilder::PAYMENT => [
            self::CREDIT_CARD => [
              self::CARDNUMBER => $payment->getCcNumber(),
              self::HOLDER => $payment->getCcOwner(),
              self::EXPIRATIONDATE => str_pad($payment->getCcExpMonth(), 2, '0', STR_PAD_LEFT) . "/" . $payment->getCcExpYear(),
              self::SECURITYCODE => $payment->getCcCid(),
              self::SAVECARD => false,
              self::BRAND =>$this->getBrandCard($payment->getCcType()),
              self::CARDTOKEN =>''
            ]
          ]
        ];
        return $result;
    }
}

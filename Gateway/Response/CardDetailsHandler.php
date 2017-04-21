<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Response;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Setor7\Cielo\Gateway\Config\Config;
use Setor7\Cielo\Model\Adminhtml\Source\EnvironmentCielo;


/**
 * Class CardDetailsHandler
 */
class CardDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader, Config $config)
    {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {

        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $response_obj = $this->subjectReader->readTransaction($response);

        $payment = $paymentDO->getPayment();

        $payment_response = $response_obj->getPayment();
        $creditCard = $payment_response->getCreditCard();

        $payment->setCcLast4($creditCard->getCardNumber());
        $payment->setCcExpMonth(substr($creditCard->getExpirationDate(),0,2));
        $payment->setCcExpYear(substr($creditCard->getExpirationDate(),3,4));

        $payment->setCcType($creditCard->getBrand());

        // set card details to additional info
        $payment->setAdditionalInformation('Titular do Cartão', $creditCard->getHolder());
        $payment->setAdditionalInformation('Numero do Cartão', $creditCard->getCardNumber());
        $payment->setAdditionalInformation('Data de Vencimento', $creditCard->getExpirationDate());
        $payment->setAdditionalInformation('Bandeira', $creditCard->getBrand());
        $mode = 'Produção';
        if ($this->config->getEnvironment() == EnvironmentCielo::ENVIRONMENT_SANDBOX) {
            $mode = 'Sandbox';
        }
        $payment->setAdditionalInformation('Modo', $mode);
    }

}

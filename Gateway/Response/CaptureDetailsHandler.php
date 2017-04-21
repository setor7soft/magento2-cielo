<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Setor7\Cielo\Model\Adapter\CieloCodes;

class CaptureDetailsHandler implements HandlerInterface
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
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
      $paymentDO = $this->subjectReader->readPayment($handlingSubject);
      $response_obj = $this->subjectReader->readTransaction($response);

      $payment = $paymentDO->getPayment();

      $payment->setAdditionalInformation("Status - Capture", $response_obj->getStatus() .' - ' . CieloCodes::STATUS_CODE[$response_obj->getStatus()]);
      $payment->setAdditionalInformation("Codigo de Retorno - Capture", $response_obj->getReturnCode());
      $payment->setAdditionalInformation("Messagem de Retorno - Capture", $response_obj->getReturnMessage());

      /** @var $payment \Magento\Sales\Model\Order\Payment */
      //$payment->setTransactionId($response[self::TXN_ID]);
      $payment->setIsTransactionClosed(false)->setTransactionAdditionalInfo('Reponse',$response_obj->jsonSerialize());
    }
}

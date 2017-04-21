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

class PaymentDetailsHandler implements HandlerInterface
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
      $response_payment = $response_obj->getPayment();

      //if ($response_payment->getPaymentId() != $payment->getParentTransactionId()) {
      //    $payment->setTransactionId($response_payment->getPaymentId());
      //}
      $payment->setTransactionId($response_payment->getPaymentId());


      $payment->setCcTransId($response_payment->getPaymentId());
      $payment->setLastTransId($response_payment->getPaymentId());


      $payment->setAdditionalInformation("Status", $response_payment->getStatus() .' - ' . CieloCodes::STATUS_CODE[$response_payment->getStatus()]);
      $payment->setAdditionalInformation("Codigo de Retorno", $response_payment->getReturnCode());
      $payment->setAdditionalInformation("Messagem de Retorno", $response_payment->getReturnMessage());
      $payment->setAdditionalInformation("Parcelas", $response_payment->getInstallments());
      $payment->setAdditionalInformation("Tid", $response_payment->getTid());
      $payment->setAdditionalInformation("Id de Pagamento", $response_payment->getPaymentId());

      /** @var $payment \Magento\Sales\Model\Order\Payment */
      //$payment->setTransactionId($response[self::TXN_ID]);
      $payment->setIsTransactionClosed(false)->setTransactionAdditionalInfo('Reponse',$response_obj->jsonSerialize());
    }
}

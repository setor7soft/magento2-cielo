<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Setor7\Cielo\Gateway\Config\Config;
use Setor7\Cielo\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Setor7\Cielo\Gateway\Request\SaleDataBuilder;

/**
 * Payment Data Builder
 */
abstract class AbstractPaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    const COUTRY_VALUE = "BRA";
    const INTEREST_BY_MERCHANT = 'ByMerchant';
    const INTEREST_BY_ISSUER = 'ByIssuer';


    const PAYMENTTYPE_CREDITCARD = 'CreditCard';
    const PAYMENTTYPE_DEBITCARD = 'DebitCard';
    const PAYMENTTYPE_ELECTRONIC_TRANSFER = 'ElectronicTransfer';
    const PAYMENTTYPE_BOLETO = 'Boleto';
    const PROVIDER_BRADESCO = 'Bradesco';
    const PROVIDER_BANCO_DO_BRASIL = 'BancoDoBrasil';
    const PROVIDER_SIMULADO = 'Simulado';

    const PAYMENT = 'Payment';
    const SERVICETAXAMOUNT = 'ServiceTaxAmount';
    const INSTALLMENTS = 'Installments';
    const INTEREST = 'Interest';
    const CAPTURE = 'Capture';
    const AUTHENTICATE = 'Authenticate';
    const RECURRENT = 'Recurrent';
    const TID = 'Tid';
    const PROOFOFSALE = 'ProofOfSale';
    const AUTHORIZATIONCODE = 'AuthorizationCode';
    const SOFTDESCRIPTOR = 'SoftDescriptor';
    const PROVIDER = 'Provider';
    const PAYMENTID = 'PaymentId';
    const TYPE = 'Type';
    const AMOUNT = 'Amount';
    const RECEIVEDDATE = 'ReceivedDate';
    const CURRENCY = 'Currency';
    const COUNTRY = 'Country';
    const RETURNCODE = 'ReturnCode';
    const RETURNMESSAGE = 'ReturnMessage';
    const STATUS = 'Status';
    const LINKS = 'Links';

    /**
     * One-time-use token that references a payment method provided by your customer,
     * such as a credit card or PayPal account.
     *
     * The nonce serves as proof that the user has authorized payment (e.g. credit card number or PayPal details).
     * This should be sent to your server and used with any of Braintree's server-side client libraries
     * that accept new or saved payment details.
     * This can be passed instead of a payment_method_token parameter.
     */
    const PAYMENT_METHOD_NONCE = 'paymentMethodNonce';

    /**
     * The merchant account ID used to create a transaction.
     * Currency is also determined by merchant account ID.
     * If no merchant account ID is specified, Braintree will use your default merchant account.
     */
    const MERCHANT_ACCOUNT_ID = 'merchantAccountId';

    /**
     * Order ID
     */
    const ORDER_ID = 'orderId';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param Config $config
     * @param SubjectReader $subjectReader
     */
    public function __construct(Config $config, SubjectReader $subjectReader)
    {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
    }

    public function getInterest(){
      //TODO: Colocar essa opção nas configurações
      return self::INTEREST_BY_MERCHANT;
    }

    public function forceCapture(){
      //TODO: Colocar essa opção nas configurações
      return false;
    }


    /**
     * retorna o tipo de transação
     * @return const
     */
    abstract public function getTypeTransaction();



    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $installments = '1';

        if ($payment->getAdditionalInformation('number_of_installments')) {
            $installments = $payment->getAdditionalInformation('number_of_installments');
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info(" payment->getAdditionalInformation: " . $payment->getAdditionalInformation('number_of_installments'));

        $result = [];

        $result[SaleDataBuilder::SALE] = [
          self::PAYMENT => [
                self::SERVICETAXAMOUNT => 0,
                //TODO: implementar o numero de parcelas
                self::INSTALLMENTS => $installments,
                self::INTEREST => $this->getInterest(),
                self::CAPTURE => $this->forceCapture(),
                self::AUTHENTICATE => false,
                //self::RECURRENT => '',
                //self::TID => '',
                //self::PROOFOFSALE => '',
                //self::AUTHORIZATIONCODE => '',
                //self::SOFTDESCRIPTOR => '',
                //self::PROVIDER => '',
                //self::PAYMENTID => '',
                self::TYPE => $this->getTypeTransaction(),
                self::AMOUNT => $order->getGrandTotalAmount() * 100,
                //self::RECEIVEDDATE => '',
                self::CURRENCY => $order->getCurrencyCode(),
                self::COUNTRY => self::COUTRY_VALUE,
                //self::RETURNCODE => '',
                //self::RETURNMESSAGE => '',
                //self::STATUS => '',
                //self::LINKS => ''
              ]
        ];
        return $result;
    }
}

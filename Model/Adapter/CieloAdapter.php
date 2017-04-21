<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Model\Adapter;

use Setor7\Cielo\Gateway\Config\Config;
use Cielo\API30\Ecommerce\Address;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Customer;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\Request\CieloRequestException;
use Cielo\API30\Merchant;
use Setor7\Cielo\Gateway\Request\SaleDataBuilder;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Framework\Phrase;
use Setor7\Cielo\Model\Adminhtml\Source\EnvironmentCielo;
use Magento\Payment\Model\Method\Logger;


/**
 * Class CieloAdapter
 * @codeCoverageIgnore
 */
class CieloAdapter
{

    /**
     * @var Config
     */
    private $config;

    private $logger;

    /**
     * @param Config $config
     */
    public function __construct(Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getEnvironment()
    {
      $result = null;
      if ($this->config->getEnvironment() == EnvironmentCielo::ENVIRONMENT_SANDBOX) {
          $result = Environment::sandbox();
      }

      return $result;
    }

    public function getMerchant()
    {
      return new Merchant($this->config->getMerchantId(), $this->config->getMerchantKey());
    }

    /**
     * @param array $attributes
     * @return \Braintree\Result\Successful|\Braintree\Result\Error
     */
    public function authorize(array $attributes)
    {
      $sale = Sale::fromJson(json_encode($attributes[SaleDataBuilder::SALE]));
      $environment = $this->getEnvironment();
      $merchant = $this->getMerchant();

      $log = [
        'request' => (array) json_encode($sale),
        'method' => 'authorize',
        'payment' => 'Cielo'
      ];
      try {
          $cielo_ecommerce = new CieloEcommerce($merchant, $environment);
          $result = $cielo_ecommerce->createSale($sale);
      } catch (CieloRequestException $e) {
          $error = [];

          if($e->getCieloError()){
            $error['cod'] = $e->getCieloError()->getCode();
            $error['msg'] = $e->getCieloError()->getMessage();
          } else {
            $error['cod'] = $e->getCode();
            $error['msg'] = $e->getMessage();
          }
          $this->logger->debug($error);

          throw $e;
      }
      $log['response'] = (array) $result->jsonSerialize();
      $this->logger->debug($log);

      return $result;
    }

    /**
     * @param array $attributes
     * @return \Braintree\Result\Successful|\Braintree\Result\Error
     */
    public function capture(array $attributes)
    {
      $log = [
        'request' => (array) json_encode($attributes),
        'method' => 'capture',
        'payment' => 'Cielo'
      ];
      $environment = $this->getEnvironment();
      $merchant = $this->getMerchant();
      $this->logger->debug($log);
      try {
          $result = (new CieloEcommerce($merchant, $environment))
                      ->captureSale($attributes['PAYMENT_ID'], $attributes['AMOUNT']);
      } catch (CieloRequestException $e) {
          $error = [];

          if($e->getCieloError()){
            $error['cod'] = $e->getCieloError()->getCode();
            $error['msg'] = $e->getCieloError()->getMessage();
          } else {
            $error['cod'] = $e->getCode();
            $error['msg'] = $e->getMessage();
          }
          $this->logger->debug($error);

          throw $e;
      }
      $log['response'] = (array) $result->jsonSerialize();
      $this->logger->debug($log);

      return $result;
    }


}

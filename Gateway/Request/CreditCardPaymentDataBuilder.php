<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Request;

use Setor7\Cielo\Gateway\Request\AbstractPaymentDataBuilder;

/**
 * Payment Data Builder
 */
class CreditCardPaymentDataBuilder extends AbstractPaymentDataBuilder
{
    /**
     * retorna o tipo de transação
     * @return const
     */
    public function getTypeTransaction()
    {
      return self::PAYMENTTYPE_CREDITCARD;
    }

}

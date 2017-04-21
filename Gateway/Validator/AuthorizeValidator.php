<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Validator;


use Setor7\Cielo\Model\Adapter\CieloCodes;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class AuthorizeValidator
 */
class AuthorizeValidator extends GeneralResponseValidator
{
    /**
     * @return array
     */
    protected function getResponseValidators()
    {
        return array_merge(
            parent::getResponseValidators(),
            [
                function ($response) {
                    return [
                        in_array(
                            $response->getPayment()->getReturnCode(),
                            [
                              CieloCodes::AUTORIZADA_COM_SUCESSO,
                              CieloCodes::AUTORIZADA_COM_SUCESSO_2,
                              CieloCodes::AUTORIZADA_COM_SUCESSO_3,
                              CieloCodes::AUTORIZADO_SANDBOX
                            ]
                        ),
                        [__('Ocorreu Um erro ao tentar realizar a transação: msg: '. $response->getPayment()->getReturnMessage() .', cod:' . $response->getPayment()->getReturnCode())]
                    ];
                }
            ]
        );
    }
}

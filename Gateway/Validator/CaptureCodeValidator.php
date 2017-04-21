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
class CaptureCodeValidator extends GeneralResponseValidator
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
                            $response->getReturnCode(),
                            [
                              CieloCodes::CAPTURE_OK
                            ]
                        ),
                        [__('Ocorreu Um erro ao tentar realizar a captura da transação: msg: '. $response->getReturnMessage() .', cod:' . $response->getReturnCode())]
                    ];
                }
            ]
        );
    }
}

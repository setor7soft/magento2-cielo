<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Setor7\Cielo\Gateway\Config\Config;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\App\RequestInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'cielo';

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var array
     */

    private $icons = [];
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $clientToken = '';

    /**
     * @var \Magento\Framework\View\Asset\Source
     */
    protected $assetSource;

    /**
     * Constructor
     *
     * @param Config $config
     * @param PayPalConfig $payPalConfig;
     * @param BraintreeAdapter $adapter
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        Config $config,
        Repository $assetRepo,
        ResolverInterface $localeResolver,
        Source $assetSource,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->assetRepo = $assetRepo;
        $this->localeResolver = $localeResolver;
        $this->assetSource = $assetSource;
        $this->request = $request;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        /*
        $hasInstallments = false;
        $installments = $this->config->getInstallments();
        if ($installments) {
          $hasInstallments = true;
        }
        */
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'countrySpecificCardTypes' => $this->config->getCountrySpecificCardTypeConfig(),
                    'availableCardTypes' => $this->config->getAvailableCardTypes(),
                    'useCvv' => $this->config->isCvvEnabled(),
                    'environment' => $this->config->getEnvironment(),
                    //'hasInstallments' => $hasInstallments,
                    //'installments' => $installments,
                    'number_installments' => $this->config->getNumberInstallments(),
                    'min_total_installments' => $this->config->getMinTotalInstallments(),
                ],
                'ccform' => [
                    'icons' => [
                        'ELO' => $this->getIcons("ELO"),
                        'AU' => $this->getIcons("AU")
                      ]
                ]
            ]
        ];
    }

    /**
     * Get icons for available payment methods
     * @param {String} card_type
     * @return array
     */
    public function getIcons($card_type)
    {
        $icon;
        if ($card_type) {
            $asset = $this->createAsset('Setor7_Cielo::images/cc/' . strtolower($card_type) . '.png');
            $placeholder = $this->assetSource->findSource($asset);
            if ($placeholder) {
                list($width, $height) = getimagesize($asset->getSourceFile());
                $icon = [
                    'url' => $asset->getUrl(),
                    'width' => $width,
                    'height' => $height
                ];
            }
        }
        return $icon;
    }

    /**
     * Create a file asset that's subject of fallback system
     *
     * @param string $fileId
     * @param array $params
     * @return \Magento\Framework\View\Asset\File
     */
    public function createAsset($fileId, array $params = [])
    {

        $params = array_merge(['_secure' => $this->request->isSecure()], $params);
        return $this->assetRepo->createAsset($fileId, $params);
    }
}

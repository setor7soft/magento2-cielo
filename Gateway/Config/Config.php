<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Setor7\Cielo\Gateway\Config;


/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ENVIRONMENT = 'environment';
    const KEY_ACTIVE = 'active';
    const KEY_MERCHANT_ID = 'merchant_id';
    const KEY_MERCHANT_KEY = 'merchant_key';

    const KEY_PRIVATE_KEY = 'private_key';
    const KEY_COUNTRY_CREDIT_CARD = 'countrycreditcard';
    const KEY_CC_TYPES = 'cctypes';
    const KEY_SDK_URL = 'sdk_url';
    const KEY_USE_CVV = 'useccv';
    const KEY_VERIFY_3DSECURE = 'verify_3dsecure';
    const KEY_THRESHOLD_AMOUNT = 'threshold_amount';
    const KEY_VERIFY_ALLOW_SPECIFIC = 'verify_all_countries';
    const KEY_VERIFY_SPECIFIC = 'verify_specific_countries';
    const VALUE_3DSECURE_ALL = 0;
    const CODE_3DSECURE = 'three_d_secure';
    const KEY_KOUNT_MERCHANT_ID = 'kount_id';
    const FRAUD_PROTECTION = 'fraudprotection';
    const INSTALLMENTS = 'installments';
    const NUMBER_INSTALLMENTS = 'number_installments';
    const MIN_TOTAL_INSTALLMENTS = 'min_total_installments';

    /**
     * Return the country specific card type config
     *
     * @return array
     */
    public function getCountrySpecificCardTypeConfig()
    {
        $countriesCardTypes = unserialize($this->getValue(self::KEY_COUNTRY_CREDIT_CARD));

        return is_array($countriesCardTypes) ? $countriesCardTypes : [];
    }

    /**
     * Return the country specific card type config
     *
     * @return array
     */
    public function getInstallments()
    {
        $installments = unserialize($this->getValue(self::INSTALLMENTS));

        return is_array($installments) ? $installments : [];
    }

    /**
     * Return the country specific card type config
     *
     * @return array
     */
    public function getNumberInstallments()
    {
        return (double) $this->getValue(self::NUMBER_INSTALLMENTS);
    }

    /**
     * Return the country specific card type config
     *
     * @return array
     */
    public function getMinTotalInstallments()
    {
        return (double) $this->getValue(self::MIN_TOTAL_INSTALLMENTS);
    }

    /**
     * Retrieve available credit card types
     *
     * @return array
     */
    public function getAvailableCardTypes()
    {
        $ccTypes = $this->getValue(self::KEY_CC_TYPES);

        return !empty($ccTypes) ? explode(',', $ccTypes) : [];
    }

    /**
     * Retrieve mapper between Magento and Braintree card types
     *
     * @return array
     */
    public function getCcTypesMapper()
    {
        $result = json_decode(
            $this->getValue(self::KEY_CC_TYPES),
            true
        );

        return is_array($result) ? $result : [];
    }



    /**
     * Get list of card types available for country
     * @param string $country
     * @return array
     */
    public function getCountryAvailableCardTypes($country)
    {
        $types = $this->getCountrySpecificCardTypeConfig();

        return (!empty($types[$country])) ? $types[$country] : [];
    }

    /**
     * Check if cvv field is enabled
     * @return boolean
     */
    public function isCvvEnabled()
    {
        return (bool) $this->getValue(self::KEY_USE_CVV);
    }

    /**
     * Check if 3d secure verification enabled
     * @return bool
     */
    public function isVerify3DSecure()
    {
        return (bool) $this->getValue(self::KEY_VERIFY_3DSECURE);
    }

    /**
     * Get threshold amount for 3d secure
     * @return float
     */
    public function getThresholdAmount()
    {
        return (double) $this->getValue(self::KEY_THRESHOLD_AMOUNT);
    }

    /**
     * Get list of specific countries for 3d secure
     * @return array
     */
    public function get3DSecureSpecificCountries()
    {
        if ((int) $this->getValue(self::KEY_VERIFY_ALLOW_SPECIFIC) == self::VALUE_3DSECURE_ALL) {
            return [];
        }

        return explode(',', $this->getValue(self::KEY_VERIFY_SPECIFIC));
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getValue(Config::KEY_ENVIRONMENT);
    }

    /**
     * @return string
     */
    public function getKountMerchantId()
    {
        return $this->getValue(Config::KEY_KOUNT_MERCHANT_ID);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getValue(Config::KEY_MERCHANT_ID);
    }

    /**
     * @return string
     */
    public function getMerchantKey()
    {
        return $this->getValue(Config::KEY_MERCHANT_KEY);
    }

    /**
     * @return string
     */
    public function getSdkUrl()
    {
        return $this->getValue(Config::KEY_SDK_URL);
    }

    /**
     * @return bool
     */
    public function hasFraudProtection()
    {
        return (bool) $this->getValue(Config::FRAUD_PROTECTION);
    }

    /**
     * Get Payment configuration status
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }
}

<?php

namespace Convert\CarbonFootprint\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    protected ScopeConfigInterface $scopeConfig;
    protected const CONFIG_WAREHOUSE_LOCATION = 'carbon_footprint/carbon_footprint/warehouse_location';
    protected const CONFIG_API_KEY = 'carbon_footprint/carbon_footprint/api_key';

    protected const CONFIG_CARBON_FOOTPRINT_INDEX = 'carbon_footprint/carbon_footprint/carbon_footprint_index';

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getWarehouseLocation(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_WAREHOUSE_LOCATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return (string)($this->scopeConfig->getValue(
            self::CONFIG_API_KEY,
            ScopeInterface::SCOPE_STORE
        ) ?? '');
    }

    /**
     * @return float
     */
    public function getCarbonFootprintIndex(): float
    {
        return (float)$this->scopeConfig->getValue(
            self::CONFIG_CARBON_FOOTPRINT_INDEX,
            ScopeInterface::SCOPE_STORE
        );
    }
}

<?php

namespace Convert\CarbonFootprint\Observer;

use Convert\CarbonFootprint\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface;

class QuoteCarbonFootprint implements ObserverInterface
{
    protected Curl $curl;
    protected LoggerInterface $logger;
    protected Config $config;

    public function __construct(Curl $curl, LoggerInterface $logger, Config $config)
    {
        $this->curl = $curl;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress || !$shippingAddress->getPostcode()) {
            return;
        }
        $origin = $this->config->getWarehouseLocation();
        $destination = $this->getFullAddress($shippingAddress);
        $distanceKm = $this->getDistance($origin, $destination);

        if ($distanceKm === null) {
            $this->logger->warning("Unable to calculate distance for: $destination");
            return;
        }

        $weight = $shippingAddress->getWeight() ?: 1;
        $carbonFootprint_index = $this->config->getCarbonFootprintIndex();


        $carbonFootprint = round($distanceKm * $weight * $carbonFootprint_index, 2);

        $quote->setCarbonFootprint($carbonFootprint);
    }

    /**
     * @param Address $address
     * @return string
     */
    private function getFullAddress(Address $address): string
    {
        return implode(', ', array_filter([
            $address->getStreetLine(1),
            $address->getCity(),
            $address->getPostcode(),
            $address->getCountryId()
        ]));
    }

    /**
     * @param string $origin
     * @param string $destination
     * @return float|int|null
     */
    private function getDistance(string $origin, string $destination): float|int|null
    {
        $apiKey = $this->config->getApiKey();

        $url = "https://api.distancematrix.ai/maps/api/distancematrix/json?origins=" .
            urlencode($origin) . "&destinations=" . urlencode($destination) .
            "&key=" . $apiKey;

        try {
            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);

            if (!empty($response['rows'][0]['elements'][0]['distance']['value'])) {
                $meters = $response['rows'][0]['elements'][0]['distance']['value'];
                return $meters / 1000;
            }
        } catch (\Exception $e) {
            $this->logger->error('Error fetching distance from API: ' . $e->getMessage());
        }

        return null;
    }
}

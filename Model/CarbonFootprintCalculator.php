<?php

namespace Convert\CarbonFootprint\Model;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class CarbonFootprintCalculator
{
    protected OrderCollectionFactory $orderCollectionFactory;

    public function __construct(OrderCollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @return array
     */
    public function getMonthlyCarbonData(): array
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToSelect(['created_at', 'carbon_footprint']);

        $now = new \DateTimeImmutable();
        $start = $now->modify('-11 months')->modify('first day of this month')->setTime(0, 0);
        $end = $now->modify('last day of this month')->setTime(23, 59, 59);

        $collection->addFieldToFilter('created_at', ['gteq' => $start->format('Y-m-d H:i:s')]);
        $collection->addFieldToFilter('created_at', ['lteq' => $end->format('Y-m-d H:i:s')]);

        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $label = $start->modify("+$i months")->format('Y-m');
            $monthlyData[$label] = 0;
        }

        foreach ($collection as $order) {
            $createdAt = $order->getCreatedAt();
            try {
                $month = (new \DateTimeImmutable($createdAt))->format('Y-m');
            } catch (\Exception $e) {
                continue;
            }

            $carbon = (float)$order->getData('carbon_footprint');
            if (isset($monthlyData[$month])) {
                $monthlyData[$month] += $carbon;
            }
        }

        ksort($monthlyData);
        return $monthlyData;
    }
}

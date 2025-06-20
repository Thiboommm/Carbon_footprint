<?php

namespace Convert\CarbonFootprint\Controller\Adminhtml\Index;

use Convert\CarbonFootprint\Model\CarbonFootprintCalculator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Chart extends Action
{
    const ADMIN_RESOURCE = 'Convert_CarbonFootprint::report';
    protected JsonFactory $resultJsonFactory;
    protected OrderCollectionFactory $orderCollectionFactory;
    protected CarbonFootprintCalculator $calculator;

    public function __construct(
        Context                   $context,
        JsonFactory               $resultJsonFactory,
        OrderCollectionFactory    $orderCollectionFactory,
        CarbonFootprintCalculator $calculator
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->calculator = $calculator;
        parent::__construct($context);
    }

    /**
     * @return Json|ResultInterface|ResponseInterface
     */
    public function execute(): Json|ResultInterface|ResponseInterface
    {
        $result = $this->resultJsonFactory->create();
        $monthlyData = $this->calculator->getMonthlyCarbonData();

        $labels = [];
        foreach (array_keys($monthlyData) as $month) {
            $labels[] = date('M Y', strtotime($month));
        }

        $result->setData([
            'labels' => $labels,
            'data' => array_values($monthlyData)
        ]);

        return $result;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Convert_CarbonFootprint::report_chart');
    }
}

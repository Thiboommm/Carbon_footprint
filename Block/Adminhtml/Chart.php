<?php

namespace Convert\CarbonFootprint\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

class Chart extends Template
{
    protected UrlInterface $urlBuilder;

    public function __construct(
        Context $context,
        array   $data = []
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getChartDataUrl(): string
    {
        return $this->urlBuilder->getUrl('convert_carbonfootprint/index/chart');
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Plugin\Ajax;

class CmsControllerIndexIndexAjax extends Ajax
{
    /** @var \Amasty\Shopby\Model\Layer\Cms\Manager  */
    protected $cmsManager;

    /**
     * CategoryViewAjax constructor.
     *
     * @param \Amasty\Shopby\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Amasty\Shopby\Helper\Data $helper,
        \Amasty\Shopby\Helper\UrlBuilder $urlBuilder,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager

    )
    {
        $this->cmsManager = $cmsManager;
        parent::__construct($helper, $urlBuilder, $resultRawFactory);
    }


    public function afterExecute(
        \Magento\Cms\Controller\Index\Index $action,
        $resultPage
    ){
        $cmsBlock = null;

        foreach($resultPage->getLayout()->getAllBlocks() as $cmsBlock){
            if ($cmsBlock instanceof \Magento\Cms\Block\Widget\Block){

                $cmsBlock->toHtml();
                foreach($resultPage->getLayout()->getAllBlocks() as $block){
                    if ($block->getData('use_improved_navigation') == 1 &&
                        $block->getProductCollection() instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection){

                        $this->cmsManager->setCmsCollection($block->getProductCollection());
                        break;
                    }
                }
                if ($this->cmsManager->isCmsPageNavigation()){
                    break;
                }


            }
        }

        $cmsBlock->getLayout()->unsetElement('widget.products.list.pager');
        
        if(!$this->isAjax($action) || !$resultPage instanceof \Magento\Framework\View\Result\Page )
        {
            return $resultPage;
        }

        $responseData = $this->getAjaxResponseData($resultPage);

        if ($cmsBlock){
            $responseData['cmsPageData'] = $cmsBlock->toHtml();
            $cmsBlock->getLayout()->unsetElement('widget.products.list.pager');
        }

        $response = $this->prepareResponse($responseData);

        return $response;
    }
}
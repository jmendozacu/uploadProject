<?php

namespace ThinkIdeas\Customerattribute\Plugin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Redirect
{
    protected $coreRegistry;

    protected $url;

    protected $resultFactory;

    public function __construct(Registry $registry, UrlInterface $url, ResultFactory $resultFactory)
    {
        $this->coreRegistry = $registry;
        $this->url = $url;
        $this->resultFactory = $resultFactory;
    }

    public function aroundGetRedirect ($subject, \Closure $proceed)
    {
        if ($this->coreRegistry->registry('is_new_account')) {
            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            // echo"<pre/>"; print_r($this->url->getUrl('customer/account/welcome/'));exit;
            $result->setUrl($this->url->getUrl('customerattribute/account/success/welcome'));
            // echo"<pre/>"; print_r($result->getUrl());exit;
            return $result;
        }

        return $proceed();
    }
}
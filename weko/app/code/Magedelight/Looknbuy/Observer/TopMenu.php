<?php

namespace Magedelight\Looknbuy\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;

class TopMenu implements ObserverInterface
{
    protected $request;
    protected $scopeConfig;

    /**
     * url builder.
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
    Http $request, UrlInterface $urlBuilder, \Magento\Store\Model\StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @param $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {

        /* @var \Magento\Framework\Data\Tree\Node $menu */
        $isEnable = $this->scopeConfig->getValue('looknbuy/general/enable', ScopeInterface::SCOPE_STORES);

        $isCatEnable = $this->scopeConfig->getValue('looknbuy/general/enabled_look_on', ScopeInterface::SCOPE_STORES) == 'topnavagation' ? true : false;

        if ($isEnable && $isCatEnable) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $urlKey = trim($this->scopeConfig->getValue('looknbuy/general/url_key', $storeScope), '/');
            $suffix = trim($this->scopeConfig->getValue('looknbuy/general/url_suffix', $storeScope), '/');
            $urlKey .= (strlen($suffix) > 0 || $suffix != '') ? '.'.str_replace('.', '', $suffix) : '/';

            $url = $this->_storeManager->getStore()->getBaseUrl().$urlKey;

            $title = $this->scopeConfig->getValue('looknbuy/general/link_title', $storeScope);

            $menu = $observer->getMenu();
            $tree = $menu->getTree();
            $looknbuyNodeId = 'looknbuy';
            $data = [
                'name' => __($title),
                'id' => $looknbuyNodeId,
                'url' => $url,
            ];
            $looknbuyNode = new Node($data, 'id', $tree, $menu);
            $menu->addChild($looknbuyNode);
        }

        return $this;
    }
}

<?php

namespace Magedelight\Looknbuy\Observer;

use Magento\Framework\Event\ObserverInterface;

class Util implements ObserverInterface
{   
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *  @var storeManager
     */
    protected $_store;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_context;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
            \Magento\Framework\View\Element\BlockFactory $blockFactory,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Store\Model\Store $store,
            \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
            \Magento\Framework\App\Action\Context $context
    ) {
        $this->_blockFactory = $blockFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_store = $store;
        $this->_scopeResolver = $scopeResolver;
        $this->messageManager = $context->getMessageManager();
        $this->_urlBuilder = $context->getUrl();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent()->getName();
        $errorMsg = $this->checkModuleActivation();
        if (count($errorMsg)) {
            foreach ($errorMsg as $msg) {
                $this->messageManager->addError($msg);
            }

            if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
                $keys['serial_key'] = $this->_scopeConfig->getValue('looknbuy/license/serial_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $keys['activation_key'] = $this->_scopeConfig->getValue('looknbuy/license/activation_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $url = $this->_urlBuilder->getCurrentUrl();
                $parsedUrl = parse_url($url);
                $keys['host'] = $parsedUrl['host'];
                $keys['ip'] = $_SERVER['SERVER_ADDR'];
                $keys['product_name'] = 'Loon N Buy';
                $field_string = http_build_query($keys);
                $ch = curl_init('http://www.magedelight.com/ktplsys/?'.$field_string);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                try {
                    curl_exec($ch);
                    curl_close($ch);
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
    }

    public function checkModuleActivation()
    {
        $messages = array();
        $serial = $this->_scopeConfig->getValue('looknbuy/license/serial_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $activation = $this->_scopeConfig->getValue('looknbuy/license/activation_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            if ($serial == '') {
                $messages[] = __("Serial key not found.Please enter valid serial key for 'Look N Buy' extension.");
            }
            if ($activation == '') {
                $messages[] = __("Activation key not found.Please enter valid activation key for 'Look N Buy' extension.");
            }
            $isValidActivation = $this->validateActivationKey($activation, $serial);
            if (count($isValidActivation)) {
                $messages[] = $isValidActivation[0];
            }
        }

        return $messages;
    }

    public function validateActivationKey($activation, $serial)
    {
        $url = $this->_urlBuilder->getCurrentUrl();
        $parsedUrl = parse_url($url);

        // Remove wwww., http:// or https:// from url.
        $domain = str_replace(array('www.', 'http://', 'https://'), '', $parsedUrl['host']);
        $hash = $serial.''.$domain;
        $message = array();
        if (md5($hash) != $activation) {
            $devPart = strchr($domain, '.', true);
            $origPart = str_replace($devPart.'.', '', $domain);
            $hash2 = $serial.''.$origPart;
            if (md5($hash2) != $activation) {
                $message[] = "Activation key invalid of 'Look N Buy' extension for this url.";
            }
        }

        return $message;
    }
}

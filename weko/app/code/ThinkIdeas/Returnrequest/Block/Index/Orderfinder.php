<?php

namespace ThinkIdeas\Returnrequest\Block\Index;

class Orderfinder extends \Magento\Framework\View\Element\Template
{

    protected $_messageManager;
    protected $_coreRegistry = null;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = [])
    {

        $this->_messageManager = $messageManager;

        $this->_coreRegistry = $context->getRegistry();
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        //  $this->addMessages($this->_messageManager->getMessages(true));
        return parent::_prepareLayout();
    }

    public function getFormAction()
    {

        return $this->getUrl('returnrequest/index/orderitemsucess', ['_secure' => true]);
    }

    public function getMessages()
    {
        $messages = array();
        $collection = $this->_messageManager->getMessages(true);
        if ($collection && $collection->getItems()) {
            foreach ($collection->getItems() as $message) {
                $messages[] = $message->getText();
            }
        }
        return $messages;
    }

    public function getOrdernorrq()
    {
        return $this->_coreRegistry->registry('rrq_order_id');
    }

}

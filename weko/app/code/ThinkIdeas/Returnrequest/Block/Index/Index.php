<?php

namespace ThinkIdeas\Returnrequest\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{

    protected $_messageManager;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = [])
    {

        $this->_messageManager = $messageManager;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        //  $this->addMessages($this->_messageManager->getMessages(true));
        return parent::_prepareLayout();
    }

    public function getFormAction()
    {
        return $this->getUrl('returnrequest/index/orderfinder', ['_secure' => true]);
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

}

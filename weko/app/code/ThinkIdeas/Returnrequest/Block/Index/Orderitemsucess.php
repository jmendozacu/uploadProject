<?php

namespace ThinkIdeas\Returnrequest\Block\Index;

class Orderitemsucess extends \Magento\Framework\View\Element\Template
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
        // this will be the same cause we dont new new controller to redirect so we are forwarding the same request to same controoler
        //so we will identify request in the observer that is it for the item request by identifing the form id
        // if yes than we will save data in observer and redirect to previous form with succuessfull message other wise same form with error.

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

    public function getSaveddataofrrq()
    {
        return $this->_coreRegistry->registry('model_saved');
    }


}

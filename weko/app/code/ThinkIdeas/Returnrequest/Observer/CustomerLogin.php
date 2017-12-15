<?php

namespace ThinkIdeas\Returnrequest\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Request\DataPersistorInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_order;
    protected $_rrqsession;

    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param CaptchaStringResolver $captchaStringResolver
     */
    public function __construct(
    \Magento\Framework\App\ActionFlag $actionFlag, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\Response\RedirectInterface $redirect, \Magento\Sales\Api\Data\OrderInterface $order, \Magento\Customer\Model\Session $rrqsession
    )
    {
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->_order = $order;
        $this->_rrqsession = $rrqsession;
    }

    /**
     * This is the method that fires when the event runs. 
     * 
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {

        $controller = $observer->getControllerAction();

        if ($this->_rrqsession->isLoggedIn()) {

            if ($controller->getRequest()->getPostValue("hideit") == "orderidform") {
                $orderv = $this->_order->loadByIncrementId($controller->getRequest()->getPostValue("orderno"));
                
                if (empty($orderv->getData())) {

                    $formId = 'returnrequest_orderid';
                    $this->messageManager->addError(__('Incorrect Order No #' . $controller->getRequest()->getPostValue("orderno")));
                    $this->getDataPersistor()->set($formId, $controller->getRequest()->getPostValue());
                    $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    $this->redirect->redirect($controller->getResponse(), 'returnrequest/index/index');
                }
                if ($orderv->getData("status")=="pending")
                 {

                    $formId = 'returnrequest_orderid';
                    $this->messageManager->addError(__('Bestellung #' . $controller->getRequest()->getPostValue("orderno")." ist ausstehend."));
                    $this->getDataPersistor()->set($formId, $controller->getRequest()->getPostValue());
                    $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    $this->redirect->redirect($controller->getResponse(), 'returnrequest/index/index');
                }
            }
        } else {

            $this->messageManager->addError(__("Please logged in as customer to request for item return."));
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->redirect->redirect($controller->getResponse(), 'returnrequest/index/index');
        }
    }
    /**
    * orderidform
    */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                    ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

}

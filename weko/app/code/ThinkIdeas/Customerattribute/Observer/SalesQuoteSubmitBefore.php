<?php
namespace ThinkIdeas\Customerattribute\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteSubmitBefore implements ObserverInterface
{   
    protected $_quote = null;

    protected $_checkoutSession;

    protected $_quoteRepository;

    public function __construct(       
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();            
        $quote = $this->_quoteRepository->get($order->getQuoteId());
        
        $customerId =  $this->customerSession->getId();
        if($customerId){
            $customerObj = $this->customerSession->getCustomer();

            $isWekoNumberExist = $customerObj->getData('weko_card_number');
            if(!empty($quote->getWekoCardNumber())){
                $isWekoNumberExist = $quote->getWekoCardNumber();
            }
            $isDobExist = $customerObj->getData('dob');
            if(!empty($quote->getDob())){
                $isDobExist = $quote->getDob();
            }           
            $order->setData('weko_card_number', $isWekoNumberExist);
            $order->setData('dob', $isDobExist);
        }else{
            $order->setData('weko_card_number', $quote->getWekoCardNumber());
            $order->setData('dob', $quote->getDob()); 
        }
        
    }

    public function getQuote()
    {
        if ($this->_quote === null) {
            return $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }
}

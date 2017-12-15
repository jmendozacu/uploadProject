<?php

namespace ThinkIdeas\Checkoutsteps\Controller\Cart;
use Magento\Framework\Controller\ResultFactory;
class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $couponFactory,
            $quoteRepository
        );

        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get('Magento\Framework\Escaper');
                if (!$itemsCount) {
                    if ($isCodeLengthValid) {
                        $coupon = $this->couponFactory->create();
                        $coupon->load($couponCode, 'code');
                        if ($coupon->getId()) {
                            $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                            $this->messageManager->addSuccess(
                                __(
                                    'You used coupon code "%1".',
                                    $escaper->escapeHtml($couponCode)
                                )
                            );
                            $data = __('You used coupon code "%1".'.$escaper->escapeHtml($couponCode));
                        } else {
                            $this->messageManager->addError(
                                __(
                                    'The coupon code "%1" is not valid.',
                                    $escaper->escapeHtml($couponCode)
                                )
                            );
                            $data = __('The coupon code "%1" is not valid.'.$escaper->escapeHtml($couponCode));
                        }
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                        $data = __('The coupon code "%1" is not valid.'.$escaper->escapeHtml($couponCode));
                    }
                } else {
                    if ($isCodeLengthValid && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccess(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                        $data = __('You used coupon code "%1".'.$escaper->escapeHtml($couponCode));
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                        $data = __('The coupon code "%1" is not valid.'.$escaper->escapeHtml($couponCode));
                        $this->cart->save();
                    }
                }
            } else {
                $this->messageManager->addSuccess(__('You canceled the coupon code.'));
                $data = __('You canceled the coupon code.');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $data = $e->getMessage();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the coupon code.'));
            $data = __('We cannot apply the coupon code.');
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        $resultJson = $this->resultJsonFactory->create();
        //echo $this->getLayout()->createBlock("Magento\Checkout\Block\Cart")->setTemplate("cart.phtml")->toHtml();
        $response = array();
        $review = $this->layoutFactory->create()->createBlock('Magento\Checkout\Block\Cart')->setTemplate('ThinkIdeas_Checkoutsteps::cart.phtml')->toHtml();
        $response['review'] = $review;
        /*print_r($data123);
        exit;*/
        //echo $this->getLayout()->getBlock('checkout.cart')->toHtml();
        /*$this->getLayout()->createBlock('Amasty\Shopby\Block\Navigation\Widget\Tooltip')
            ->setFilterSetting($setting)
            ->toHtml();*/
        //exit;
        return $resultJson->setData($response);
        //return $resultJson;


        /* Custom code added for redirect checkout order summary */
        /*$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl()."#order_summary");
        return $resultRedirect;*/
        /* Custom code ended */

        //return $this->_goBack(); /* defual code commented */
    }
}
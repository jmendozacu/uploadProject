<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magedelight\Looknbuy\Controller\Wishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @param Action\Context                                         $context
     * @param \Magento\Customer\Model\Session                        $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface                             $productRepository
     */
    public function __construct(
    Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, ProductRepositoryInterface $productRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->_postDataHelper = $postDataHelper;
        parent::__construct($context);
        $this->productRepository = $productRepository;
    }

    /**
     * Adding new item.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     *
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $session = $this->_customerSession;

        $lookId = $this->getRequest()->getParam('look_id');

        $products = json_decode($this->getProducts());

        foreach ($products as $product) {
            $_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product->pid);

            $requestParams = json_decode($this->_objectManager->create('Magento\Wishlist\Helper\Data')->getAddParams($_product), true)['data'];

            if ($session->getBeforeWishlistRequest()) {
                // $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }

            $productId = isset($requestParams['product']) ? (int) $requestParams['product'] : null;
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            if (!$productId) {
                $resultRedirect->setPath('*/');

                return $resultRedirect;
            }

            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product || !$product->isVisibleInCatalog()) {
                $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
                $resultRedirect->setPath('looknbuy/index/looks');

                return $resultRedirect;
            }

            try {
                $buyRequest = new \Magento\Framework\DataObject($requestParams);

                $result = $wishlist->addNewItem($product, $buyRequest);
                if (is_string($result)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($result));
                }
                $wishlist->save();

                $this->_eventManager->dispatch(
                        'wishlist_add_product', ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
                );

                $referer = $session->getBeforeWishlistUrl();
                if ($referer) {
                    $session->setBeforeWishlistUrl(null);
                } else {
                    $referer = $this->_redirect->getRefererUrl();
                }

                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

                $this->messageManager->addComplexSuccessMessage(
                        'addProductSuccessMessage', [
                    'product_name' => $product->getName(),
                    'referer' => $referer,
                        ]
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                        __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                        $e, __('We can\'t add the item to Wish List right now.')
                );
            }
        }

        $resultRedirect->setPath('wishlist/index/index', ['wishlist_id' => $wishlist->getId()]);

        return $resultRedirect;
    }

    public function getProducts()
    {
        $lookId = $this->getRequest()->getParam('look_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                        ->getCollection()
                        ->addFieldToSelect('*')->addFieldToFilter('look_id', array('eq' => $lookId));

        $finaldata = array();
        $k = 0;
        $productObj = array();
        foreach ($optionCollection as $key => $option) {
            $finaldata[$key]['id'] = $key;
            $finaldata[$key]['pname'] = htmlspecialchars($option['product_name']);
            $finaldata[$key]['pid'] = $option['product_id'];
            $finaldata[$key]['psku'] = $option['sku'];
            $finaldata[$key]['price'] = $option['price'];
            $finaldata[$key]['qty'] = $option['qty'];

            ++$k;
        }

        return json_encode($finaldata);
    }
}

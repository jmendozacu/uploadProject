<?php
declare(strict_types=1);
/**
 * ThinkIdeas_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  ThinkIdeas
 * @package   ThinkIdeas_Storelocator
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
namespace ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;

use ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;
use ThinkIdeas\Storelocator\Controller\RegistryConstants;

class Edit extends Stores
{
    /**
     * Initialize current stockist and set it in the registry.
     *
     * @return int
     */
    public function _initStockist()
    {
        $stockistId = $this->getRequest()->getParam('stockist_id');
        $this->coreRegistry->register(RegistryConstants::CURRENT_STOCKIST_ID, $stockistId);

        return $stockistId;
    }

    /**
     * Edit or create stockist
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $stockistId = $this->_initStockist();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ThinkIdeas_Storelocator::stores');
        $resultPage->getConfig()->getTitle()->prepend(__('Storelocator'));
        $resultPage->addBreadcrumb(__('Storelocator'), __('Storelocator'), $this->getUrl('storelocator/stores'));

        if ($stockistId === null) {
            $resultPage->addBreadcrumb(__('New Store'), __('New Store'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Store'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Store'), __('Edit Store'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->stockistRepository->getById($stockistId)->getName()
            );
        }
        return $resultPage;
    }
}

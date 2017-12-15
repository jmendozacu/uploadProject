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

use \ThinkIdeas\Storelocator\Controller\Adminhtml\Stores as StockistController;

class Index extends StockistController
{
    /**
     * Storelocator list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ThinkIdeas_Storelocator::stores');
        $resultPage->getConfig()->getTitle()->prepend(__('Storelocator'));
        $resultPage->addBreadcrumb(__('Storelocator'), __('Storelocator'));
        return $resultPage;
    }
}

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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;

class Delete extends Stores
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('stockist_id');
        if ($id) {
            try {
                $this->stockistRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The stockist has been deleted.'));
                $resultRedirect->setPath('storelocator/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The stockist no longer exists.'));
                return $resultRedirect->setPath('storelocator/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('storelocator/stores/edit', ['stockist_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the stockist'));
                return $resultRedirect->setPath('storelocator/stores/edit', ['stockist_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a stockist to delete.'));
        $resultRedirect->setPath('storelocator/*/');
        return $resultRedirect;
    }
}

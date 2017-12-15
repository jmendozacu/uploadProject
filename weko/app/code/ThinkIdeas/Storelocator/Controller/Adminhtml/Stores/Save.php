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

use Magento\Backend\Model\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use ThinkIdeas\Storelocator\Api\StockistRepositoryInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterfaceFactory;
use ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;
use ThinkIdeas\Storelocator\Model\Uploader;
use ThinkIdeas\Storelocator\Model\UploaderPool;

class Save extends Stores
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * @param Registry $registry
     * @param StockistRepositoryInterface $stockistRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param StockistInterfaceFactory $stockistFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param UploaderPool $uploaderPool
     */
    public function __construct(
        Registry $registry,
        StockistRepositoryInterface $stockistRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        StockistInterfaceFactory $stockistFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        UploaderPool $uploaderPool
    ) {
        $this->stockistFactory = $stockistFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($registry, $stockistRepository, $resultPageFactory, $dateFilter, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        /** @var \ThinkIdeas\Storelocator\Api\Data\StockistInterface $stockist */
        $stockist = null;
        $data = $this->getRequest()->getPostValue();
        $categoryPostData = $data;
        $id = !empty($data['stockist_id']) ? $data['stockist_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('ThinkIdeas\Storelocator\Model\Stores');

        try {
            if ($id) {
                $model->load($id);
            } else {
                unset($data['stockist_id']);
                $stockist = $this->stockistFactory->create();
            }
            $image = $this->getUploader('image')->uploadFileAndGetName('image', $data);
            $data['image'] = $image;
            $this->dataObjectHelper->populateWithArray($stockist, $data, StockistInterface::class);
            $model->setData($data);
            $model->save();

            /**
             * Process "Product save" 
             */
            if (isset($categoryPostData['store_products'])
                && is_string($categoryPostData['store_products'])
            ) {
                $this->setPostedProducts($model,$categoryPostData);
            }

            $this->messageManager->addSuccessMessage(__('You saved the store'));
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('storelocator/stores/edit', ['stockist_id' => $model->getId()]);
            } else {
                $resultRedirect->setPath('storelocator/stores');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($model != null) {
                $this->storeStockistDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $model,
                        StockistInterface::class
                    )
                );
            }
            $resultRedirect->setPath('storelocator/stores/edit', ['stockist_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the store'));
            if ($model != null) {
                $this->storeStockistDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $model,
                        StockistInterface::class
                    )
                );
            }
            $resultRedirect->setPath('storelocator/stores/edit', ['stockist_id' => $id]);
        }
        return $resultRedirect;
    }

    public function setPostedProducts($model, $post)
    {
        $products['products'] = json_decode($post['store_products'], true);
        if (isset($products)) {
            $productIds = $products['products'];

            try {

                $oldProducts = $model->getStoreProducts($model);
                $newProducts = (array) $productIds;

                $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                $connection = $this->_resources->getConnection();

                $table = $this->_resources->getTableName(\ThinkIdeas\Storelocator\Model\ResourceModel\Stores::TBL_ATT_PRODUCT);
                $insert = array_diff_key($newProducts, $oldProducts);
                $delete = array_diff_key($oldProducts, $newProducts);

                $update = array_intersect_key($newProducts, $oldProducts);
                $update = array_diff_assoc($update, $oldProducts);


                if ($delete) {
                    $deletepro = array_keys($delete);
                    $where = ['product_id IN(?)' => $deletepro, 'stockist_id=?' => (int)$model->getId()];
                    $connection->delete($table, $where);
                }

                if ($insert) {
                    $data = [];

                    foreach ($insert as $key => $product_data) {
                        $data[] = ['stockist_id' => (int)$model->getId(), 'product_id' => (int)$key, 'product_quantity' => $product_data];
                    }
                    $connection->insertMultiple($table, $data);
                }

                /**
                 * Update product product_quantitys in category
                 */
                if (!empty($update)) {
                    foreach ($update as $key => $productQty) {
                        $where = ['stockist_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$key];
                        $bind = ['product_quantity' => (int)$productQty];
                        $connection->update($table, $bind, $where);
                    }
                }
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
            }
        }

    }

    /**
     * @param $type
     * @return Uploader
     * @throws \Exception
     */
    public function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }

    /**
     * @param $stockistData
     */
    public function storeStockistDataToSession($stockistData)
    {
        $this->_getSession()->setThinkIdeasStorelocatorStoresData($stockistData);
    }
}

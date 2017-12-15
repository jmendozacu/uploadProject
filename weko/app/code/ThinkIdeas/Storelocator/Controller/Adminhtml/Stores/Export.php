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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\Component\MassAction\Filter;
use ThinkIdeas\Storelocator\Api\StockistRepositoryInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterfaceFactory;
use ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;
use ThinkIdeas\Storelocator\Model\Uploader;
use ThinkIdeas\Storelocator\Model\UploaderPool;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\CollectionFactory;



class Export extends Stores
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    /**

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
        UploaderPool $uploaderPool,
        FileFactory $fileFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->stockistFactory = $stockistFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($registry, $stockistRepository, $resultPageFactory, $dateFilter, $context);
    }
    
    /**
     * Export data grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        try {
            
            $content = '';
            $content .= '"store_id",';
            $content .= '"name",';
            $content .= '"address",';
            $content .= '"city",';
            $content .= '"country",';
            $content .= '"postcode",';
            $content .= '"region",';
            $content .= '"email",';
            $content .= '"phone",';
            $content .= '"link",';
            $content .= '"image",';
            $content .= '"latitude",';
            $content .= '"longitude",';
            $content .= '"status",';
            $content .= '"updated_at",';
            $content .= '"created_at"';
            $content .= "\n";
            
            $fileName = 'storelocator_export.csv';
            $collection = $this->collectionFactory->create()->getData();
            
            foreach ($collection as $stockist) {
                array_shift($stockist); //skip the id
                $content .= implode(",", array_map([$this, 'addQuotationMarks'],$stockist));
                $content .= "\n";
            }

            return $this->fileFactory->create(
                $fileName,
                $content,
                DirectoryList::VAR_DIR
            );
            
            $this->messageManager->addSuccessMessage(__('You exported the file. It can be found in var folder or in browser downloads.'));
            $resultRedirect->setPath('storelocator/stores');
            
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem exporting the data'));
            $resultRedirect->setPath('storelocator/stores/export');
        }
        
        return $resultRedirect;

    }
    
     /**
     * Add quotes to fields
     * @param string
     * @return string
     */
    public function addQuotationMarks($row)
    {
        return sprintf('"%s"', $row);
    }
}
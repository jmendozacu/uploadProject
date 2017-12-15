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
use Magento\Framework\File\Csv;
use ThinkIdeas\Storelocator\Api\StockistRepositoryInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterfaceFactory;
use ThinkIdeas\Storelocator\Controller\Adminhtml\Stores;
use ThinkIdeas\Storelocator\Model\Uploader;
use ThinkIdeas\Storelocator\Model\UploaderPool;


class ImportFile extends Stores
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
     * @var csvProcessor
     */
    public $csvProcessor;


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
        Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
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
        $stockist = null;
        $data = $this->getRequest()->getPostValue();
        $filePath = $data["import"][0]["path"].$data["import"][0]["file"];
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($data["import"][0]["path"] && $data["import"][0]["file"]) {
            
            try {
                $rawStockistData = $this->csvProcessor->getData($filePath);
                
                // first row of file represents headers
                $fileHeaders = $rawStockistData[0];
                $processedStockistData = $this->filterFileData($fileHeaders, $rawStockistData);
            
                foreach($processedStockistData as $individualStockist) {
                    
                    $stockistId = !empty($individualStockist['stockist_id']) ? $individualStockist['stockist_id'] : null;
                    
                    if ($stockistId) {
                        $stockist = $this->stockistRepository->getById((int)$stockistId);
                    } else {
                        unset($individualStockist['stockist_id']);
                        $stockist = $this->stockistFactory->create();
                    }
        
                    $this->dataObjectHelper->populateWithArray($stockist,$individualStockist,StockistInterface::class);
                    $this->stockistRepository->save($stockist);
                }
    
                $this->messageManager->addSuccessMessage(__('Your file has been imported successfully'));
                
                $resultRedirect->setPath('storelocator/stores');                    
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($stockist != null) {
                    $this->storeStockistDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $stockist,
                            StockistInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('storelocator/stores/edit');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was an error importing the file'));
                if ($stockist != null) {
                    $this->storeStockistDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $stockist,
                            StockistInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('storelocator/stores/import');
            }
                
        } else {
            $this->messageManager->addError(__('Please upload a file'));
        }

        return $resultRedirect;
    }

    /**
     * Filter data so that it will skip empty rows and headers
     *
     * @param   array $fileHeaders
     * @param   array $rawStockistData
     * @return  array
     */
    public function filterFileData(array $fileHeaders, array $rawStockistData)
    {
        $rowCount=0;
        $rawDataRows = [];
        
        foreach ($rawStockistData as $rowIndex => $dataRow) {
            
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($rawStockistData[$rowIndex]);
                continue;
            }
            /* we take rows from [0] = > value to [website] = base */
            if ($rowIndex > 0) {
                foreach ($dataRow as $rowIndex => $dataRowNew) {
                    $rawDataRows[$rowCount][$fileHeaders[$rowIndex]] = $dataRowNew;
                }
            }
            $rowCount++;
        }
        return $rawDataRows;
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
    
}

<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use ThinkIdeas\Bannerslider\Model\Slide\ImageFileUploader;
use ThinkIdeas\Bannerslider\Model\Source\ImageType;
use Magento\Framework\App\RequestInterface;
use ThinkIdeas\Bannerslider\Model\ResourceModel\Slide\Grid\CollectionFactory;

/**
 * Class SlideDataProvider
 * @package ThinkIdeas\Bannerslider\Ui\DataProvider
 */
class SlideDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param ImageFileUploader $imageFileUploader
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        ImageFileUploader $imageFileUploader,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_bannerslider_slide');
        if (!empty($dataFromForm)) {
            $data[$dataFromForm['id']] = $dataFromForm;
            $this->dataPersistor->clear('aw_bannerslider_slide');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            /** @var \ThinkIdeas\Bannerslider\Model\Slide $slide */
            foreach ($this->getCollection()->getItems() as $slide) {
                if ($id == $slide->getId()) {
                    $data[$id] = $this->prepareFormData($slide->getData());
                }
            }
        }
        return $data;
    }

    /**
     * Prepare form data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareFormData(array $itemData)
    {
        if ($itemData['img_type'] == ImageType::TYPE_FILE) {
            $itemData['img_file'] = [
                0 => [
                    'file' => $itemData['img_file'],
                    'url' => $this->imageFileUploader->getMediaUrl($itemData['img_file'])
                ]
            ];
        }
        return $itemData;
    }
}

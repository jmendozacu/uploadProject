<?php

/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>.
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 *
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Looknbuy\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    protected $_storeManager;
    protected $_backjshelper;

    /**
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Backend\Helper\Js                       $backjshelper
     * @param \Magento\Framework\Image\AdapterFactory          $adapterFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploader
     * @param \Magento\Framework\Filesystem                    $filesystem
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Backend\Helper\Js $backjshelper, \Magento\Framework\Image\AdapterFactory $adapterFactory, \Magento\MediaStorage\Model\File\UploaderFactory $uploader, \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_storeManager = $storeManager;
        $this->_backjshelper = $backjshelper;

        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Magedelight\Looknbuy\Model\Looknbuy');

            $id = $this->getRequest()->getParam('look_id');
            if ($data['category_ids']) {
                $data['category_ids'] = str_replace(' ', '', $data['category_ids']);
            }
            if ($id) {
                $model->load($id);
            }
            try {
                $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'base_image']);

                if (isset($uploader->validateFile()['tmp_name']) && $uploader->validateFile()['tmp_name'] != '') {
                    $mediapathget = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                    $uploader = $this->_objectManager->create(
                            'Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'base_image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                            ->getDirectoryRead(DirectoryList::MEDIA);
                    #$config = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('look'));
                    $data['base_image'] = 'look'.$result['file'];
                } else {
                    if (isset($data['base_image']['delete']) && $data['base_image']['delete'] == 1) {
                        $data['base_image'] = '';
                    } else {
                        unset($data['base_image']);
                    }
                }
            } catch (\Exception $e) {
                if ($e->getCode() == '666') {
                    if (isset($data['base_image']['delete']) && $data['base_image']['delete'] == 1) {
                        $data['base_image'] = '';
                    } else {
                        unset($data['base_image']);
                    }

                    //return $this;
                } else {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['look_id' => $this->getRequest()->getParam('look_id')]);
                    
                }
            }
            $urlKey = $data['url_key'];
            
            if($urlKey == ''){
                $newUrlKey = preg_replace('#[^0-9a-z]+#i', '-', $data['look_name']);
                $newUrlKey = strtolower($newUrlKey);
                
                $data['url_key'] = $newUrlKey;
            }
            $model->setData($data);

            $colection = $this->_objectManager->create('Magedelight\Looknbuy\Model\Looknbuy')->getCollection()->addFieldToSelect('*')->addFieldToFilter('url_key', array('eq' => $urlKey));

            if (count($colection) > 0 && !$id) {
                $this->messageManager->addError(__('URL key already exists.'));
                $this->_getSession()->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', ['look_id' => $this->getRequest()->getParam('look_id')]);
            }

            $model->save();
            
            if (isset($data['option'])) {
                foreach ($data['option'] as $key => $_options) {
                    foreach ($_options as $k => $value) {
                        if ($value['lid'] == '' || $value['lid'] == null) {
                            $value['lid'] = $model->getId();
                        }

                        if ($key == 'value') {
                            /* ---Delete--- */
                            if ($value['del'] == 1 && is_int($k)) {
                                $lookDel = $this->_objectManager->create('Magedelight\Looknbuy\Model\Lookitems')
                                        ->load($k)
                                        ->delete();
                            }

                            /* ---Insert---- */
                            $lookItems = $this->_objectManager->create('Magedelight\Looknbuy\Model\Lookitems');

                            if (is_int($k)) {
                                $lookItems->setId($k);
                            }

                            $lookItems->setLookId(trim($value['lid']))
                                    ->setProductId(trim($value['pid']))
                                    ->setProductName(trim($value['pname']))
                                    ->setPrice(trim($value['price']))
                                    ->setSku(trim($value['psku']))
                                    ->setQty(trim($value['qty']));

                            if ($value['del'] == 1 && !is_int($k)) {
                                unset($_options[$k]);
                            } else {
                                $lookItems->save();
                            }
                        }
                    }
                }
            }

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this Look.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['look_id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the look.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['look_id' => $this->getRequest()->getParam('look_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

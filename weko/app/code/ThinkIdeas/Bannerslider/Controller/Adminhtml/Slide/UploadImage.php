<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use ThinkIdeas\Bannerslider\Model\Slide\ImageFileUploader;

/**
 * Class UploadImage
 * @package ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide
 */
class UploadImage extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'ThinkIdeas_Bannerslider::slides';

    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param Context $context
     * @param ImageFileUploader $imageFileUploader
     */
    public function __construct(
        Context $context,
        ImageFileUploader $imageFileUploader
    ) {
        parent::__construct($context);
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * Image upload action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->imageFileUploader->saveImageToMediaFolder('img_file');
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}

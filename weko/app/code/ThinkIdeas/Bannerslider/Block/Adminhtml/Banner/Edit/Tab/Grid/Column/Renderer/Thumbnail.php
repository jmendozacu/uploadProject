<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;

use ThinkIdeas\Bannerslider\Model\Source\ImageType;
use Magento\Backend\Block\Context;
use ThinkIdeas\Bannerslider\Model\Slide\ImageFileUploader;

/**
 * Class Thumbnail
 * @package ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer
 */
class Thumbnail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param Context $context
     * @param ImageFileUploader $imageFileUploader
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageFileUploader $imageFileUploader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getImgType() == ImageType::TYPE_FILE) {
            $imgUrl = $this->imageFileUploader->getMediaUrl($row->getImgFile());
        } else {
            $imgUrl = $row->getImgUrl();
        }
        return '<img width="200" src="' . $imgUrl . '"/>';
    }
}

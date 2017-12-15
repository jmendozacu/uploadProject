<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;

/**
 * Class SlideName
 * @package ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer
 */
class SlideName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $url = $this->getUrl(
            'aw_bannerslider_admin/slide/edit',
            ['id' => $row->getId()]
        );
        return '<a href="' . $url . '" target="_blank">' . $row->getName() . '</a>';
    }
}

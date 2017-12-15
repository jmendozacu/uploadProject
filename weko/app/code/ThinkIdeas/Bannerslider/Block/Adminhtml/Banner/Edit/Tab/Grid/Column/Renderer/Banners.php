<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;

use Magento\Framework\DataObject;

/**
 * Class Banners
 * @package ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer
 */
class Banners extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $bannerUrlsRow = [];
        if (count($row->getBannerIds())) {
            $columnOptions = $this->getColumn()->getOptions();
            foreach ($row->getBannerIds() as $id) {
                $name = (is_array($columnOptions) && isset($columnOptions[$id]))
                    ? $columnOptions[$id]
                    : $id;
                $url = $this->getUrl(
                    'aw_bannerslider_admin/banner/edit',
                    ['id' => $id]
                );
                $bannerUrlsRow[] = '<a href="' . $url . '" target="_blank">' . $name . '</a>';
            }
        }
        return implode(', ', $bannerUrlsRow);
    }
}

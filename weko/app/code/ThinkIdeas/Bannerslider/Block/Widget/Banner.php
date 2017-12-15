<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Block\Widget;

use ThinkIdeas\Bannerslider\Model\Source\Position;
use ThinkIdeas\Bannerslider\Model\Source\PageType;
use ThinkIdeas\Bannerslider\Api\Data\BlockInterface;

/**
 * Class Banner
 * @package Magento\Blog\Block\Widget
 */
class Banner extends \ThinkIdeas\Bannerslider\Block\Banner implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    const WIDGET_NAME_PREFIX = 'aw_bannerslider_widget_';

    /**
     * Retrieve banner for widget
     *
     * @return BlockInterface[]
     */
    public function getBlocks()
    {
        $bannerId = $this->getData('banner_id');
        $blocks = $this->blocksRepository
            ->getList(PageType::CUSTOM_WIDGET, Position::CONTENT_TOP)
            ->getItems();

        foreach ($blocks as $block) {
            if ($block->getBanner()->getId() == $bannerId) {
                return [$block];
            }
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNameInLayout()
    {
        return self::WIDGET_NAME_PREFIX . $this->getData('banner_id');
    }
}

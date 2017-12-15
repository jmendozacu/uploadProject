<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab;

use ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Slide;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Slides
 *
 * @package ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Tab
 */
class Slides extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'banner/edit/slides.phtml';

    /**
     * @var Slide
     */
    private $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid()
    {
        if (!$this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Slide::class,
                'slide.banner.grid'
            );
        }
        return $this->blockGrid;
    }
}

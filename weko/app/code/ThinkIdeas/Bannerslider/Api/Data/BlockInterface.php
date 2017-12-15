<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api\Data;

use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Api\Data\SlideInterface;
use ThinkIdeas\Bannerslider\Api\Data\BlockExtensionInterface;

/**
 * Block interface
 * @api
 */
interface BlockInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BANNER = 'banner';
    const SLIDES = 'slides';
    /**#@-*/

    /**
     * Get banner
     *
     * @return BannerInterface|null
     */
    public function getBanner();

    /**
     * Set banner
     *
     * @param BannerInterface $banner
     * @return BlockInterface
     */
    public function setBanner($banner);

    /**
     * Get slides
     *
     * @return SlideInterface[]|null
     */
    public function getSlides();

    /**
     * Set slides
     *
     * @param SlideInterface[] $slides
     * @return BlockInterface
     */
    public function setSlides($slides);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return BlockExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param BlockExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(BlockExtensionInterface $extensionAttributes);
}

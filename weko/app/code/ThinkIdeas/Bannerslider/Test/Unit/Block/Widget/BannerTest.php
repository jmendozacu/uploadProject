<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Block\Widget;

use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Block\Widget\Banner;
use ThinkIdeas\Bannerslider\Api\BlockRepositoryInterface;
use ThinkIdeas\Bannerslider\Api\Data\BlockInterface;
use ThinkIdeas\Bannerslider\Api\Data\BlockSearchResultsInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \ThinkIdeas\Bannerslider\Block\Widget\Banner
 */
class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Banner
     */
    private $block;

    /**
     * @var BlockRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blocksRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blocksRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->block = $objectManager->getObject(
            Banner::class,
            [
                'blocksRepository' => $this->blocksRepositoryMock
            ]
        );
    }

    /**
     * Testing of getBlocks method
     */
    public function testGetBlocks()
    {
        $bannerId = 1;

        $this->block->setData('banner_id', $bannerId);
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock->expects($this->once())
            ->method('getBanner')
            ->willReturn($bannerMock);
        $blockSearchResultsMock = $this->getMockForAbstractClass(BlockSearchResultsInterface::class);
        $blockSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$blockMock]);
        $this->blocksRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($blockSearchResultsMock);

        $this->assertSame([$blockMock], $this->block->getBlocks());
    }

    /**
     * Testing of getNameInLayout method
     */
    public function testGetNameInLayout()
    {
        $bannerId = 1;
        $expected = Banner::WIDGET_NAME_PREFIX . $bannerId;

        $this->block->setData('banner_id', $bannerId);
        $this->assertEquals($expected, $this->block->getNameInLayout());
    }
}

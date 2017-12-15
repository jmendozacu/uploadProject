<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Block\Adminhtml\Banner\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\Delete;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use ThinkIdeas\Bannerslider\Api\BannerRepositoryInterface;
use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use Magento\Framework\App\Request\Http;

/**
 * Test for \ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\Delete
 */
class DeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Delete
     */
    private $button;

    /**
     * @var BannerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerRepositoryMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMock(Http::class, ['getParam'], [], '', false);
        $this->bannerRepositoryMock = $this->getMockForAbstractClass(BannerRepositoryInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );
        $this->button = $objectManager->getObject(
            Delete::class,
            [
                'context' => $contextMock,
                'bannerRepository' => $this->bannerRepositoryMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $bannerId = 1;
        $deleteUrl = 'https://ecommerce.thinkIdeas.com/index.php/admin/aw_bannerslider_admin/banner/delete/id/' . $bannerId;

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/delete'),
                $this->equalTo(['id' => $bannerId])
            )->willReturn($deleteUrl);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($bannerId);

        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $this->bannerRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($bannerId)
            ->willReturn($bannerMock);

        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}

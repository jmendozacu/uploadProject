<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Block;

use ThinkIdeas\Bannerslider\Block\Ajax;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout\ProcessorInterface;

/**
 * Test for \ThinkIdeas\Bannerslider\Block\Ajax
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AjaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ajax
     */
    private $block;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'layout' => $this->layoutMock,
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );

        $this->block = $objectManager->getObject(
            Ajax::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of getScriptOptions method
     */
    public function testGetScriptOptions()
    {
        $isSecure = false;
        $url = 'https://ecommerce.thinkIdeas.com/aw_bannerslider/block/render/id/1369/';
        $handles = ['handle_1', 'handle_2'];
        $expected = '{"url":"https:\/\/ecommerce.thinkIdeas.com\/aw_bannerslider\/block\/render\/id\/1369\/",'
            . '"handles":["handle_1","handle_2"]}';

        $this->requestMock->expects($this->once())
            ->method('isSecure')
            ->willReturn($isSecure);
        $layoutUpdateMock = $this->getMockForAbstractClass(ProcessorInterface::class);
        $layoutUpdateMock->expects($this->once())
            ->method('getHandles')
            ->willReturn($handles);
        $this->layoutMock->expects($this->once())
            ->method('getUpdate')
            ->willReturn($layoutUpdateMock);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'aw_bannerslider/block/render/',
                [
                    '_current' => true,
                    '_secure' => $isSecure,
                ]
            )->willReturn($url);

        $this->assertEquals($expected, $this->block->getScriptOptions());
    }
}

<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Block\Adminhtml\Banner\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\Back;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

/**
 * Test for \ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\Back
 */
class BackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Back
     */
    private $button;
    
    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $backUrl = 'https://ecommerce.thinkIdeas.com/index.php/admin/aw_bannerslider_admin/banner';
        $urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/')
            ->willReturn($backUrl);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $urlBuilderMock
            ]
        );
        $this->button = $objectManager->getObject(
            Back::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}

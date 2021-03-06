<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide\NewAction;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide\NewAction
 */
class NewActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewAction
     */
    private $controller;

    /**
     * @var ForwardFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $forwardFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->forwardFactoryMock = $this->getMock(ForwardFactory::class, ['create'], [], '', false);
        $contextMock = $objectManager->getObject(
            Context::class,
            []
        );

        $this->controller = $objectManager->getObject(
            NewAction::class,
            [
                'context' => $contextMock,
                'resultForwardFactory' => $this->forwardFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $resultForwardMock = $this->getMock(Forward::class, ['forward'], [], '', false);
        $resultForwardMock->expects($this->once())
            ->method('forward')
            ->willReturnSelf();
        $this->forwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultForwardMock);

        $this->assertSame($resultForwardMock, $this->controller->execute());
    }
}

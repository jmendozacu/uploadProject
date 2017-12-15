<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide\UploadImage;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Json as ResultJson;
use ThinkIdeas\Bannerslider\Model\Slide\ImageFileUploader;

/**
 * Test for \ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide\UploadImage
 */
class UploadImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UploadImage
     */
    private $controller;

    /**
     * @var ImageFileUploader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFileUploaderMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultFactoryMock = $this->getMock(ResultFactory::class, ['create'], [], '', false);
        $this->imageFileUploaderMock = $this->getMock(
            ImageFileUploader::class,
            ['saveImageToMediaFolder'],
            [],
            '',
            false
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultFactory' => $this->resultFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            UploadImage::class,
            [
                'context' => $contextMock,
                'imageFileUploader' => $this->imageFileUploaderMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $result = [
            'size' => 282567,
            'file' => '1.png',
            'url' => 'https://ecommerce.thinkIdeas.com/pub/media/aw_bannerslider/slides/1.png'
        ];

        $this->imageFileUploaderMock->expects($this->once())
            ->method('saveImageToMediaFolder')
            ->with('img_file')
            ->willReturn($result);
        $resultJsonMock = $this->getMock(ResultJson::class, ['setData'], [], '', false);
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }
}

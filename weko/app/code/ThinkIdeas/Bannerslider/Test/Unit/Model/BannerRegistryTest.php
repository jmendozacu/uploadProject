<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Model;

use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Model\BannerRegistry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test \ThinkIdeas\Bannerslider\Model\BannerRegistry
 */
class BannerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BannerRegistry
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            BannerRegistry::class,
            []
        );
    }

    /**
     * Testing of retrieve method on null
     */
    public function testRetrieveNull()
    {
        $bannerId = 1;
        $this->assertNull($this->model->retrieve($bannerId));
    }

    /**
     * Testing of retrieve method on object
     */
    public function testRetrieveObject()
    {
        $bannerId = 1;
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $this->model->push($bannerMock);
        $this->assertEquals($bannerMock, $this->model->retrieve($bannerId));
    }

    /**
     * Testing of remove method
     */
    public function testRemove()
    {
        $bannerId = 1;
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $this->model->push($bannerMock);

        $bannerFromReg = $this->model->retrieve($bannerId);
        $this->assertEquals($bannerMock, $bannerFromReg);
        $this->model->remove($bannerId);
        $this->assertNull($this->model->retrieve($bannerId));
    }
}

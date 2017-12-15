<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Model\Source;

use ThinkIdeas\Bannerslider\Model\Source\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test \ThinkIdeas\Bannerslider\Model\Source\Status
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Status|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Status::class,
            []
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }

    /**
     * Testing of getOptionArray method
     */
    public function testGetOptionArray()
    {
        $this->assertTrue(is_array($this->model->getOptionArray()));
    }
}

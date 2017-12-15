<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Model\Source;

use ThinkIdeas\Bannerslider\Model\Source\AnimationEffect;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \ThinkIdeas\Bannerslider\Model\Source\AnimationEffect
 */
class AnimationEffectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnimationEffect|\PHPUnit_Framework_MockObject_MockObject
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
            AnimationEffect::class,
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
}

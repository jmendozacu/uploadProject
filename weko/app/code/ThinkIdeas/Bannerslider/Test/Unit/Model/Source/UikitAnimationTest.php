<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use ThinkIdeas\Bannerslider\Model\Source\UikitAnimation;
use ThinkIdeas\Bannerslider\Model\Source\AnimationEffect;

/**
 * Test \ThinkIdeas\Bannerslider\Model\Source\UikitAnimation
 */
class UikitAnimationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UikitAnimation|\PHPUnit_Framework_MockObject_MockObject
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
            UikitAnimation::class,
            []
        );
    }

    /**
     * Testing of getAnimationEffectByKey method
     *
     * @param int $key
     * @param string $expected
     * @dataProvider getAnimationEffectByKeyDataProvider
     */
    public function testGetAnimationEffectByKey($key, $expected)
    {
        $this->assertEquals($expected, $this->model->getAnimationEffectByKey($key));
    }

    /**
     * Data provider for testGetAnimationEffectByKey method
     *
     * @return array
     */
    public function getAnimationEffectByKeyDataProvider()
    {
        return [
            [AnimationEffect::SLIDE, 'scroll'],
            [AnimationEffect::FADE_OUT_IN, 'fade'],
            [AnimationEffect::SCALE, 'scale']
        ];
    }
}

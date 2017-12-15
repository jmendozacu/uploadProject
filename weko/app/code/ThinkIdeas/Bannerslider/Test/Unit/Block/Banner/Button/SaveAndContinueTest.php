<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Block\Adminhtml\Banner\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\SaveAndContinue;

/**
 * Test for \ThinkIdeas\Bannerslider\Block\Adminhtml\Banner\Edit\Button\SaveAndContinue
 */
class SaveAndContinueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SaveAndContinue
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
        $this->button = $objectManager->getObject(
            SaveAndContinue::class,
            []
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

<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Test\Unit\Model\Rule\Viewed;

use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Model\Banner;
use ThinkIdeas\Bannerslider\Model\Rule\Validator;
use ThinkIdeas\Bannerslider\Model\Source\PageType;
use Magento\Framework\App\RequestInterface;
use ThinkIdeas\Bannerslider\Model\Rule\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \ThinkIdeas\Bannerslider\Model\Rule\Validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
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
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->model = $objectManager->getObject(
            Validator::class,
            [
                'request' => $this->requestMock
            ]
        );
    }

    /**
     * Testing of canShow method for category page
     *
     * @param string $categoryIds
     * @param bool $expected
     * @dataProvider canShowOnCategoryPageDataProvider
     */
    public function testCanShowOnCategoryPage($categoryIds, $expected)
    {
        $currentCategoryId = 1;

        $bannerMock = $this->getMock(BannerInterface::class);
        $bannerMock->expects($this->atLeastOnce())
            ->method('getCategoryIds')
            ->willReturn($categoryIds);
        $bannerMock->expects($this->once())
            ->method('getPageType')
            ->willReturn(PageType::CATEGORY_PAGE);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($currentCategoryId);

        $this->assertEquals($expected, $this->model->canShow($bannerMock));
    }

    /**
     * Data provider for testCanShowOnCategoryPage method
     *
     * @return array
     */
    public function canShowOnCategoryPageDataProvider()
    {
        return [
            ['', true],
            ['2,3,4', false],
        ];
    }

    /**
     * Testing of canShow method for product page
     *
     * @param string $categoryIds
     * @param bool $expected
     * @dataProvider canShowOnProductPageDataProvider
     */
    public function testCanShowOnProductPage($matchingProductIds, $expected)
    {
        $currentProductId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($currentProductId);

        $productRuleMock = $this->getMock(
            Product::class,
            ['getMatchingProductIds', 'getConditions'],
            [],
            '',
            false
        );
        $productRuleMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([]);
        $productRuleMock->expects($this->once())
            ->method('getMatchingProductIds')
            ->willReturn($matchingProductIds);

        $bannerMock = $this->getMock(Banner::class, ['getProductRule', 'getPageType'], [], '', false);
        $bannerMock->expects($this->exactly(2))
            ->method('getProductRule')
            ->willReturn($productRuleMock);
        $bannerMock->expects($this->once())
            ->method('getPageType')
            ->willReturn(PageType::PRODUCT_PAGE);

        $this->assertEquals($expected, $this->model->canShow($bannerMock));
    }

    /**
     * Data provider for testCanShowOnProductPage method
     *
     * @return array
     */
    public function canShowOnProductPageDataProvider()
    {
        return [
            [[1, 2, 3], true],
            [[2, 3, 4], false],
        ];
    }
}

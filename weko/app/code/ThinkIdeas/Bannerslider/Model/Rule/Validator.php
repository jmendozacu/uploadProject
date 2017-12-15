<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\Rule;

use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Model\Source\PageType;
use Magento\Framework\App\RequestInterface;
use ThinkIdeas\Bannerslider\Model\Banner;

/**
 * Class Validator
 * @package ThinkIdeas\Bannerslider\Model\Rule
 */
class Validator
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Is show banner
     *
     * @param BannerInterface|Banner $banner
     * @return bool
     */
    public function canShow(BannerInterface $banner)
    {
        switch ($banner->getPageType()) {
            case PageType::PRODUCT_PAGE:
                $currentProductId = $this->request->getParam('id');
                if (!$currentProductId) {
                    return false;
                }
                $conditions = $banner->getProductRule()->getConditions();
                if (isset($conditions)) {
                    $match = $banner->getProductRule()->getMatchingProductIds();
                    if (in_array($currentProductId, $match)) {
                        return true;
                    }
                }
                break;
            case PageType::CATEGORY_PAGE:
                $currentCategoryId = $this->request->getParam('id');
                if ($currentCategoryId && (!$banner->getCategoryIds()
                    || in_array($currentCategoryId, explode(',', $banner->getCategoryIds())))
                ) {
                    return true;
                }
                break;
            default:
                return true;
        }
        return false;
    }
}

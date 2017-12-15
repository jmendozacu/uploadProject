<?php

namespace ThinkIdeas\Additional\Helper;

use Magento\Catalog\Model\ProductFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $productFactory;
    protected $imageBuilder;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ProductFactory $productFactory,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
    ) {
        $this->productFactory = $productFactory;
        $this->imageBuilder = $imageBuilder;
        parent::__construct($context);
    }

    public function DisplayDiscountLabel($_product)
    {
        $originalPrice = $_product->getPrice();
        $finalPrice = $_product->getFinalPrice();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice,0);
        }

        if ($percentage) {
            return "-" .$percentage."%";  
        }

    }
    public function getUpsellProducts($productId)
    {
        $product  = $this->productFactory->create()->load($productId);

        // echo"<pre/>"; print_r($product->getId());exit;

        $upSellProductIds = $product->getUpSellProductIds();
 
        if(isset($upSellProductIds)){

            $products = array();
            $iterator = 1;
            foreach($upSellProductIds as $productId) {
                if ($iterator == 3) {
                    break;
                }
                $product = $this->productFactory->create()->load($productId);
                $products[] = $product;
                $iterator++;
            }

            return $products;
        }

        return null;
    }
    public function getImage($product, $imageId)
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->create();
    }
}
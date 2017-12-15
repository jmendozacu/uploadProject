<?php

namespace ThinkIdeas\TipsOfExperts\Model\Catalog\Category;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
	protected function getFieldsMap()
    {
    	$fieldMap = parent::getFieldsMap();
		$fieldMap['tips_of_experts'][] = 'block_layout';
		return $fieldMap;
    }
}


<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\Converter;

use ThinkIdeas\Bannerslider\Api\Data\ConditionInterfaceFactory;
use ThinkIdeas\Bannerslider\Api\Data\ConditionInterface;

/**
 * Class Condition
 *
 * @package ThinkIdeas\Bannerslider\Model\Converter
 */
class Condition
{
    /**
     * @var ConditionInterfaceFactory
     */
    private $conditionFactory;

    /**
     * @param ConditionInterfaceFactory $conditionFactory
     */
    public function __construct(
        ConditionInterfaceFactory $conditionFactory
    ) {
        $this->conditionFactory = $conditionFactory;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return \ThinkIdeas\Bannerslider\Api\Data\ConditionInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function arrayToDataModel(array $input)
    {
        /** @var \ThinkIdeas\Bannerslider\Model\Data\Condition $conditionDataModel */
        $conditionDataModel = $this->conditionFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $conditionDataModel->setType($value);
                    break;
                case 'attribute':
                    $conditionDataModel->setAttribute($value);
                    break;
                case 'operator':
                    $conditionDataModel->setOperator($value);
                    break;
                case 'value':
                    $conditionDataModel->setValue($value);
                    break;
                case 'value_type':
                    $conditionDataModel->setValueType($value);
                    break;
                case 'aggregator':
                    $conditionDataModel->setAggregator($value);
                    break;
                case 'bannerslider':
                case 'conditions':
                    $conditions = [];
                    /** @var array $condition */
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToDataModel($condition);
                    }
                    $conditionDataModel->setConditions($conditions);
                    break;
                default:
            }
        }
        return $conditionDataModel;
    }

    /**
     * Convert recursive condition data model into array
     *
     * @param ConditionInterface $dataModel
     * @return array
     */
    public function dataModelToArray(ConditionInterface $dataModel)
    {
        $output = [
            'type' => $dataModel->getType(),
            'attribute' => $dataModel->getAttribute(),
            'operator' => $dataModel->getOperator(),
            'value' => $dataModel->getValue(),
            'value_type' => $dataModel->getValueType(),
            'aggregator' => $dataModel->getAggregator()
        ];

        foreach ((array)$dataModel->getConditions() as $conditions) {
            $output['conditions'][] = $this->dataModelToArray($conditions);
        }
        return $output;
    }
}

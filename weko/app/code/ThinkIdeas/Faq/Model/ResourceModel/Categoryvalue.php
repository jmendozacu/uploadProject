<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model\ResourceModel;
/**
 * Class Categoryvalue
 * @package ThinkIdeas\Faq\Model\ResourceModel
 */
class Categoryvalue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('faq_category_value', 'category_value_id');
    }
}
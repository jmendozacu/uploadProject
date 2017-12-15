<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue;
/**
 * Class Collection
 * @package ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Faq\Model\Categoryvalue', 'ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue');
    }
}

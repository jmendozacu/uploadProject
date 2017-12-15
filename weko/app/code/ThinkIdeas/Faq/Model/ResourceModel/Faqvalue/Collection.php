<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model\ResourceModel\Faqvalue;
/**
 * Class Collection
 * @package ThinkIdeas\Faq\Model\ResourceModel\Faqvalue
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Faq\Model\Faqvalue', 'ThinkIdeas\Faq\Model\ResourceModel\Faqvalue');
    }
}

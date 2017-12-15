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
 * Class Faqvalue
 * @package ThinkIdeas\Faq\Model\ResourceModel
 */
class Faqvalue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('faq_value', 'faq_value_id');
    }
}

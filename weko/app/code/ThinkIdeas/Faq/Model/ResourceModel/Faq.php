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
 * Class Faq
 * @package ThinkIdeas\Faq\Model\ResourceModel
 */
class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->dateTime = $dateTime;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('faq', 'faq_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $faq
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $faq)
    {
        if ($faq->isObjectNew()) {
            $faq->setCreatedTime($this->dateTime->formatDate(true));
        }
        $faq->setUpdateTime($this->dateTime->formatDate(true));

        return parent::_beforeSave($faq);
    }
}

<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model\ResourceModel\Category;
/**
 * Class Collection
 * @package ThinkIdeas\Faq\Model\ResourceModel\Category
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var int
     */
    protected $_storeViewId;
    /**
     * @var array
     */
    protected $_addedTable = [];
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_readConnection;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection);
        $this->_storeManager = $storeManager;
        $this->_readConnection = $resourceConnection;

        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Faq\Model\Category', 'ThinkIdeas\Faq\Model\ResourceModel\Category');
    }

    /**
     * @return int
     */
    public function getStoreViewId() {
        return $this->_storeViewId;
    }

    /**
     * @param $storeViewId
     * @return $this
     */
    public function setStoreViewId($storeViewId) {
        $this->_storeViewId = $storeViewId;
        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterLoad() {
        parent::_afterLoad();
        if ($storeViewId = $this->getStoreViewId()) {
            foreach ($this->_items as $item) {
                $item->setStoreViewId($storeViewId)->getStoreViewValue();
            }
        }
        return $this;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getReadConnection()
    {
        return $this->_readConnection->getConnection();
    }

    /**
     * @param $firstCondition
     * @param $secondCondition
     * @param $type
     * @return string
     */
    protected function _implodeCondition($firstCondition, $secondCondition, $type)
    {
        return '(' . implode(') ' . $type . ' (', [$firstCondition, $secondCondition]) . ')';
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null) {
        $attributes = array(
            'name',
            'status',
            'ordering'
        );
        $storeViewId = $this->getStoreViewId();

        if (in_array($field, $attributes) && $storeViewId) {
            if (!in_array($field, $this->_addedTable)) {
                $sql = sprintf(
                    'main_table.category_id = %s.category_id AND %s.store_id = %s  AND %s.attribute_code = %s ',
                    $this->getReadConnection()->quoteTableAs($field),
                    $this->getReadConnection()->quoteTableAs($field),
                    $this->getReadConnection()->quote($storeViewId),
                    $this->getReadConnection()->quoteTableAs($field),
                    $this->getReadConnection()->quote($field)
                );

                $this->getSelect()
                    ->joinLeft(array($field => $this->getTable('faq_category_value')), $sql, array());
                $this->_addedTable[] = $field;
            }

            $fieldNullCondition = $this->_translateCondition("$field.value", ['null' => TRUE]);

            $mainfieldCondition = $this->_translateCondition("main_table.$field", $condition);

            $fieldCondition = $this->_translateCondition("$field.value", $condition);

            $condition = $this->_implodeCondition(
                $this->_implodeCondition($fieldNullCondition, $mainfieldCondition, \Zend_Db_Select::SQL_AND),
                $fieldCondition,
                \Zend_Db_Select::SQL_OR
            );

            $this->_select->where($condition, NULL, \Magento\Framework\DB\Select::TYPE_CONDITION);

            return $this;
        }
        if ($field == 'store_id') {
            $field = 'main_table.category_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }
}

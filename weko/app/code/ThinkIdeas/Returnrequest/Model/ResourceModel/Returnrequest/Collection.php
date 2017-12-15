<?php

namespace ThinkIdeas\Returnrequest\Model\ResourceModel\Returnrequest;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Returnrequest\Model\Returnrequest', 'ThinkIdeas\Returnrequest\Model\ResourceModel\Returnrequest');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}

?>
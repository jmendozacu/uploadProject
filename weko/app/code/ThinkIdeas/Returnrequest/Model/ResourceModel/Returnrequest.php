<?php

namespace ThinkIdeas\Returnrequest\Model\ResourceModel;

class Returnrequest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('returnreqest', 'rr_id');
    }

}

?>
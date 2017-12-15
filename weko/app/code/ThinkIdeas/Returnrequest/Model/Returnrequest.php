<?php

namespace ThinkIdeas\Returnrequest\Model;

class Returnrequest extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Returnrequest\Model\ResourceModel\Returnrequest');
    }

}

?>
<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ThinkIdeas\Faq\Block;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Success extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;

    public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
          parent::__construct($context); 
          $this->_objectManager  = $objectmanager;        
    }
}

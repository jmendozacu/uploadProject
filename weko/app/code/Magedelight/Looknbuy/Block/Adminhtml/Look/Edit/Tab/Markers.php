<?php

namespace Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab;

/**
 * Adminhtml look edit form.
 */
class Markers extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_template = 'markers.phtml';
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getOptionValues()
    {
        $lookId = $this->getRequest()->getParam('look_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                ->getCollection()
                ->addFieldToSelect('*')->addFieldToFilter('look_id', array('eq' => $lookId));

        $finaldata = array();
        $k = 0;
        $productObj = array();
        foreach ($optionCollection as $key => $option) {
            $finaldata[$key]['id'] = $key;
            $finaldata[$key]['pname'] = htmlspecialchars($option['product_name']);
            $finaldata[$key]['pid'] = $option['product_id'];
            $finaldata[$key]['psku'] = $option['sku'];
            $finaldata[$key]['price'] = $option['price'];
            $finaldata[$key]['qty'] = $option['qty'];

            ++$k;
        }

        return json_encode($finaldata);
    }
}

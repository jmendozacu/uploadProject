<?php

namespace Magedelight\Looknbuy\Block\Adminhtml\Look\Edit;

/**
 * Admin page left menu.
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     */
    const ADVANCED_TAB_GROUP_CODE = 'advanced';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('look_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Look Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
                'main', [
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getLayout()->createBlock(
                    'Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab\Main'
            )->toHtml(),
            'active' => true,
                ]
        );

        $this->addTab(
                'metadata', [
            'label' => __('Meta Data'),
            'title' => __('Meta Data'),
            'content' => $this->getLayout()->createBlock(
                    'Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab\Meta'
            )->toHtml(),
            'active' => false,
                ]
        );
        $serchBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab\Looks');

        $gridBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Items\Grid')->setTemplate('items/grid.phtml');

        $serchBlock->setChild('product_search_grid', $gridBlock);

        $this->addTab(
                'products', [
            'label' => __('Products'),
            'title' => __('Products'),
            'content' => $serchBlock->toHtml(),
            'active' => false,
                ]
        );

        if ($this->getRequest()->getParam('look_id')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $optionCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                            ->getCollection()
                            ->addFieldToSelect('*')->addFieldToFilter('look_id', array('eq' => $this->getRequest()->getParam('look_id')));

            if (count($optionCollection) > 0) {
                $this->addTab(
                        'add_markers', [
                    'label' => __('Add Markers'),
                    'title' => __('Add Markers'),
                             'class' => 'ajax',
                             'url' => $this->getUrl('looknbuy/*/marker', ['_current' => true]),
                    //'content' => $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab\Markers')->toHtml(),
                    'active' => true,
                        ]
                );
            }
        }

        return parent::_beforeToHtml();
    }
}

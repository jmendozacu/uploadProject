<?php

namespace Magedelight\Looknbuy\Block\Adminhtml\Look;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize look edit block.
     */
    protected function _construct()
    {
        $this->_objectId = 'look_id';
        $this->_blockGroup = 'Magedelight_Looknbuy';
        $this->_controller = 'adminhtml_look';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Look'));
        $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ],
                ],
                -100
            );

        $this->buttonList->update('delete', 'label', __('Delete Look'));
    }

    /**
     * Retrieve text for header element depending on loaded look.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('look_look')->getId()) {
            return __("Edit Look '%1'", $this->escapeHtml($this->_coreRegistry->registry('look_look')->getLookName()));
        } else {
            return __('New Look');
        }
    }

    /**
     * Check permission for passed action.
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later.
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('looknbuy/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }

    /**
     * Prepare layout.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('look_description') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'look_description');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'look_description');
                }
            };
        ";

        return parent::_prepareLayout();
    }
}

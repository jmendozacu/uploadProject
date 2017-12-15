<?php

namespace ThinkIdeas\TipsOfExperts\Block\Adminhtml;

class Options extends \Magento\Backend\Block\Template
{
	/**
     * @var string
     */
    protected $_template = 'ThinkIdeas_TipsOfExperts::options.phtml';

    /**
     * @var \Magento\Cms\Model\BlockFactory $blockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->_blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    public function getBlockArray()
    {
        $blockObject = $this->_blockFactory->create(); $resultData = [];
        foreach($blockObject->getCollection() as $block)
            $resultData[] = ['value' => $block->getId(), 'label' => $block->getTitle()];
        return $resultData;
    }
}
<?php

namespace ThinkIdeas\TipsOfExperts\Block\Adminhtml\Brand;

use Magento\Framework\Escaper;

class Field extends \Magento\Framework\Data\Form\Element\AbstractElement
{
	protected $_blockFactory;

	/**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
	public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        $data = []
    ) {
    	$this->_blockFactory = $blockFactory;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

	/**
	* Get the after element html.
	*
	* @return mixed
	*/
	public function getElementHtml()
	{
		return $this->_blockFactory->createBlock('\ThinkIdeas\TipsOfExperts\Block\Adminhtml\Options')->toHtml();
	}
}
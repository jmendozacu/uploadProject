<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Megamenu\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * Html page top menu block
 */
class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    
    public function __construct(        
        \Magento\Framework\View\Element\Template\Context $context,      
        \Magento\Framework\Data\Tree\NodeFactory $nodeFactory,
        \Magento\Framework\Data\TreeFactory $treeFactory,
        array $data = []    
    ){      
        parent::__construct($context,$nodeFactory,$treeFactory,$data);    
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {   

        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $categoryId = explode("-", $child->getId());

        $blockIdentifier= strtolower($child->getName());
        $staticblock = "";

        if ($blockIdentifier && $childLevel == 0) {

            $identifier = 'menu-'. $blockIdentifier . '-'. $categoryId[2];
            // echo"<pre/>"; print_r($identifier);
            $blockObject = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($identifier);
            // $block = $this->_blockFactory->create()->load($subBlockId);
            $blockhtml = '<li class="megaStaticBlock">';
           // echo"<pre/>"; print_r($blockObject->toHtml());exit;
            $blockhtml .= '<div>' . $blockObject->toHtml() . '</div></li>';
            $staticblock = $blockhtml;
    // echo"<pre/>"; print_r($staticblock);exit;                
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' submenu">';
        $html .= '<div class="level-123' . $childLevel . ' submenu-inner">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= $staticblock;
        if ($childLevel == '0')
        {
            $html .= '<div class="menu-body"></div>';
        }
        $html .= '</div>';
        $html .= '</ul>';

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
        $staticblock = "";
        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }


            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            if ($childLevel < 3) {
                
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . ' data-limit="' . $childLevel .'">';
                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            }


            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }
        
        return $html;
    }

    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Theme\Block\Html\Topmenu'));
        return parent::_toHtml();
    } 
}

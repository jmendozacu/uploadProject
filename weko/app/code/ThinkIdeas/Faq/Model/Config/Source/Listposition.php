<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model\Config\Source;
/**
 * Class Listposition
 * @package ThinkIdeas\Faq\Model\Config\Source
 */
class Listposition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $positionArray = array(
            array('value' => 'sidebar-right', 'label' => __('Right sidebar')),
            array('value' => 'sidebar-left', 'label' => __('Left sidebar')),
        );
        return $positionArray;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $positionArray = array(
            'sidebar-right' => __('Right sidebar'),
            'sidebar-left' => __('Left sidebar'),
        );
        return $positionArray;
    }
}

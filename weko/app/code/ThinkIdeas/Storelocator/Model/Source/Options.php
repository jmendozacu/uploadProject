<?php
declare(strict_types=1);
/**
 * ThinkIdeas_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  ThinkIdeas
 * @package   ThinkIdeas_Storelocator
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
namespace ThinkIdeas\Storelocator\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Options extends AbstractSource implements ArrayInterface
{
    /**
     * get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->options as $values) {
            $options[] = [
                'value' => $values['value'],
                'label' => __($values['label'])
            ];
        }
        return $options;

    }


}

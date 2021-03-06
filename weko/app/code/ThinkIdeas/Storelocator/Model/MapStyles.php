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
 
namespace ThinkIdeas\Storelocator\Model;

class MapStyles
{
	
    public function toOptionArray(): array
    {
        return array(
            array(
                'value' => 'default',
                'label' => 'Default Styles',
            ),
            array(
                'value' => 'ultra_light_with_labels',
                'label' => 'Ultra Light with Labels',
            ),
            array(
                'value' => 'light_dream',
                'label' => 'Light Dream',
            ),
            array(
                'value' => 'blue_water',
                'label' => 'Blue water',
            ),
            array(
                'value' => 'pale_Dawn',
                'label' => 'Pale Dawn',
            ),
            array(
                'value' => 'paper',
                'label' => 'Paper',
            ),
            array(
                'value' => 'light_monochrome',
                'label' => 'Light Monochrome',
            ),
            array(
                'value' => 'hopper',
                'label' => 'Hopper',
            )
        );
    }
    
}
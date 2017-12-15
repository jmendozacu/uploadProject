<?php
/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>.
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 *
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Looknbuy\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Store.
 */
class Image extends Column
{
    const NAME = 'base_image';

    const ALT_FIELD = 'name';

    protected $_storeManager;

    /**
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store.
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore        $systemStore
     * @param Escaper            $escaper
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
         \Magento\Customer\Model\GroupFactory $collectionFactory,
        Escaper $escaper,
        array $components = [],
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->collectionFactory = $collectionFactory;
        $this->escaper = $escaper;
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $mediaRelativePath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $logoPath = $mediaRelativePath.$item['base_image'];
                $item[$fieldName.'_src'] = $logoPath;
                $item[$fieldName.'_alt'] = $this->getAlt($item);
                $item[$fieldName.'_link'] = $this->urlBuilder->getUrl(
                    'looknbuy/index/edit',
                    ['look_id' => $item['look_id'], 'store' => $this->context->getRequestParam('store')]
                );
                $item[$fieldName.'_orig_src'] = $logoPath;
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;

        return isset($row[$altField]) ? $row[$altField] : null;
    }
}

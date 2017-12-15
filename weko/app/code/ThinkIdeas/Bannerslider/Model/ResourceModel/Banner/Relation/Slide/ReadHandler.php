<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\ResourceModel\Banner\Relation\Slide;

use Magento\Framework\App\ResourceConnection;
use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package ThinkIdeas\Bannerslider\Model\ResourceModel\Banner\Relation\Slide
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_bannerslider_slide_banner'), ['slide_id', 'position'])
                ->where('banner_id = :id');
            $slide = $connection->fetchPairs($select, ['id' => $entityId]);
            $entity->setSlideIds(array_keys($slide));
            $entity->setSlidePosition(json_encode($slide));
        }
        return $entity;
    }
}

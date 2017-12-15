<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\Adminhtml\Banner;

/**
 * Class MassStatus
 * @package ThinkIdeas\Bannerslider\Controller\Adminhtml\Banner
 */
class MassStatus extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $status = (int)$this->getRequest()->getParam('status');
        $count = 0;
        foreach ($collection->getItems() as $item) {
            $bannerDataObject = $this->bannerRepository->get($item->getId());
            $bannerDataObject->setStatus($status);
            $this->bannerRepository->save($bannerDataObject);
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated', $count));
    }
}

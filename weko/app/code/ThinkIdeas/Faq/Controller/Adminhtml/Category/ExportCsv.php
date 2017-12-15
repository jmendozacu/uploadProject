<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportCsv
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
 */
class ExportCsv extends \Magento\Backend\App\Action {
	/**
	 * @var \Magento\Framework\App\Response\Http\FileFactory
     */
	protected $_fileFactory;
	/**
	 * @var \Magento\Framework\View\Result\LayoutFactory
     */
	protected $resultLayoutFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
	 * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory
	) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
	}

	/**
	 * @return \Magento\Framework\App\ResponseInterface
	 * @throws \Exception
     */
	public function execute() {
		$fileName = 'categories.csv';
		$exportBlock = $this->resultLayoutFactory->create()
			->getLayout()->createBlock('ThinkIdeas\Faq\Block\Adminhtml\Category\Grid');
		return $this->_fileFactory->create(
			$fileName,
			$exportBlock->getCsvFile(),
			DirectoryList::VAR_DIR
		);
	}

	/**
	 * @return bool
     */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('ThinkIdeas_Faq::category');
	}
}

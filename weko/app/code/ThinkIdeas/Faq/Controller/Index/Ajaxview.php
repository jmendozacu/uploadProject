<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Ajaxview
 * @package ThinkIdeas\Faq\Controller\Index
 */
class Ajaxview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var
     */
    protected $storeManager;
    /**
     * @var
     */
    protected $_registry;
    /**
     * @var
     */
    protected $_categoryFactory;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    )
    {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     *
     */
    public function execute()
    {
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $block = $resultLayout->getLayout()->createBlock('ThinkIdeas\Faq\Block\Listfaq')
            ->setTemplate('list.phtml')->toHtml();
        echo $block;
    }
}
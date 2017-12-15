<?php
namespace ThinkIdeas\Returnrequest\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_ENABLED = 'voucher_info/general/enabled';
    const CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER = 'voucher_info/general/customer_email_sender'; //sales represent, general 1
    const CONFIG_NOTIFY_CUSTOMER_APPROVED_EMAIL = 'return_info/general/approved_email_template'; //template id
    const APPROVED_EMAIL = 'return_info/general/approved_email_template'; /*template id */
    const TYPE_ADMIN = 'admin';
    
    protected $senderEmailConfigKey = 'trans_email/ident_%s/email';
    protected $senderNameConfigKey = 'trans_email/ident_%s/name';

    protected $_storeId = null;

    protected $storeManager;

    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder        
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    public function getStore($id = null)
    {
        return $this->storeManager->getStore($id);
    }

    /**
     * Retrieve approved email template id.
     *
     * @return string|int
     */
    public function getApprovedEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_CUSTOMER_APPROVED_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender email.
     *
     * @param string     $type
     * @param string|int $storeId
     *
     * @return string
     */
    public function getSenderEmail($type = null, $storeId = null)
    {
        $identifier = $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $key = sprintf($this->senderEmailConfigKey, $identifier);
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve sender name.
     *
     * @param string     $type
     * @param string|int $storeId
     *
     * @return string
     */
    public function getSenderName($type = null, $storeId = null)
    {
            $identifier = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_CUSTOMER_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        $key = sprintf($this->senderNameConfigKey, $identifier);
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve formatted sender details.
     */
    public function getSender($type = null, $storeId = null)
    {
        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);

        $sender ['name'] = $senderName;
        $sender ['email'] = $senderEmail;
        return $sender;
    }

    /**
     * Retrieve  template id.
     */
    public function getAdminNotifyEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_NOTIFY_CUSTOMER_APPROVED_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getReturnRequestTemplateId($storeId = null){
        return $this->scopeConfig->getValue(
            self::APPROVED_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Send Email
     *
     * @param string $recipientName
     * @param string $recipientEmail
     * @param        $template
     * @param        $sender
     * @param array  $templateParams
     * @param null   $storeId
     *
     */
    public function sendEmailTemplate(
        $recipientName, $recipientEmail, $template, $sender, $templateParams = [], $storeId = null
    ) {
        // @var \Magento\Framework\Mail\Template\TransportBuilder $transport 
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $sender
        )->addTo(
            $recipientEmail, $recipientName
        )->getTransport();

        $transport->sendMessage();

        return $this;
    }

}

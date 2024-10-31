<?php declare(strict_types=1);
/**
 * @author    Osiozekhai Aliu
 * @package   Osio_MagentoMailAttachment
 * @copyright Copyright (c) 2024 Osio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osio\MagentoMailAttachment\Model;

use Laminas\Mime\Mime;
use Laminas\Mime\PartFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as TransportBuilderAlias;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Osio\MagentoMailAttachment\Api\TransportBuilderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TransportBuilder extends TransportBuilderAlias implements TransportBuilderInterface
{

    /**
     * @var int
     */
    protected const TYPE_TEXT = 1;

    /**
     * @var int
     */
    protected const  TYPE_HTML = 2;

    /**
     * @var array
     */
    private array $messageData = [];

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var AddressConverter|null
     */
    private $addressConverter;

    /**
     * @var array
     */
    protected array $attachments = [];

    /**
     * @var PartFactory|mixed
     */
    protected PartFactory $partFactory;

    /**
     * @var Escaper
     */
    private Escaper $escaper;

    /**
     * @param                                          Escaper                           $escaper
     * @param                                          FactoryInterface                  $templateFactory
     * @param                                          MessageInterface                  $message
     * @param                                          SenderResolverInterface           $senderResolver
     * @param                                          ObjectManagerInterface            $objectManager
     * @param                                          TransportInterfaceFactory         $mailTransportFactory
     * @param                                          MessageInterfaceFactory|null      $messageFactory
     * @param                                          EmailMessageInterfaceFactory|null $emailMessageInterfaceFactory
     * @param                                          MimeMessageInterfaceFactory|null  $mimeMessageInterfaceFactory
     * @param                                          MimePartInterfaceFactory|null     $mimePartInterfaceFactory
     * @param                                          AddressConverter|null             $addressConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Escaper                      $escaper,
        FactoryInterface             $templateFactory,
        MessageInterface             $message,
        SenderResolverInterface      $senderResolver,
        ObjectManagerInterface       $objectManager,
        TransportInterfaceFactory    $mailTransportFactory,
        MessageInterfaceFactory      $messageFactory = null,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null,
        MimeMessageInterfaceFactory  $mimeMessageInterfaceFactory = null,
        MimePartInterfaceFactory     $mimePartInterfaceFactory = null,
        AddressConverter             $addressConverter = null
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory,
            $messageFactory,
            $emailMessageInterfaceFactory,
            $mimeMessageInterfaceFactory,
            $mimePartInterfaceFactory,
            $addressConverter
        );
        $this->templateFactory = $templateFactory;
        $this->objectManager = $objectManager;
        $this->_senderResolver = $senderResolver;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory ?: $this->objectManager
            ->get(EmailMessageInterfaceFactory::class);
        $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory ?: $this->objectManager
            ->get(MimeMessageInterfaceFactory::class);
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory ?: $this->objectManager
            ->get(MimePartInterfaceFactory::class);
        $this->addressConverter = $addressConverter ?: $this->objectManager
            ->get(AddressConverter::class);
        $this->partFactory = $objectManager->get(PartFactory::class);
        $this->escaper = $escaper;
    }

    /**
     * @inheritDoc
     */
    public function addCc($address, $name = ''): TransportBuilder
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTo($address, $name = ''): TransportBuilder
    {
        try {
            $this->addAddressByType('to', $address, $name);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBcc($address): TransportBuilder
    {
        try {
            $this->addAddressByType('bcc', $address);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setReplyTo($email, $name = null): TransportBuilder
    {
        try {

            $this->addAddressByType('replyTo', $email, $name);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFrom($from): ?TransportBuilder
    {
        try {
            return $this->setFromByScope($from);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function setFromByScope($from, $scopeId = null): TransportBuilder
    {
        try {
            $result = $this->_senderResolver->resolve($from, $scopeId);
            $this->addAddressByType('from', $result['email'], $result['name']);
        } catch (InvalidArgumentException|MailException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateIdentifier($templateIdentifier): TransportBuilder
    {
        $this->templateIdentifier = $templateIdentifier;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateModel($templateModel): TransportBuilder
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateVars($templateVars): TransportBuilder
    {
        $this->templateVars = $templateVars;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateOptions($templateOptions): TransportBuilder
    {
        $this->templateOptions = $templateOptions;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTransport(): TransportInterface
    {
        try {
            $this->prepareMessage();
            $mailTransport = $this->mailTransportFactory->create(['message' => clone $this->message]);
        } finally {
            $this->reset();
        }

        return $mailTransport;
    }

    /**
     * @inheritDoc
     */
    public function addAttachment(?string $content, ?string $fileName, ?string $fileType): TransportBuilder
    {
        $attachmentPart = $this->partFactory->create();
        $attachmentPart->setContent($content)
            ->setType($fileType)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
            ->setEncoding(Mime::ENCODING_BASE64);
        $this->attachments[] = $attachmentPart;

        return $this;
    }

    /**
     * Reset object state
     *
     * @return $this
     */
    protected function reset(): TransportBuilder
    {
        $this->messageData = [];
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;
        return $this;
    }

    /**
     * Get template
     *
     * @return TemplateInterface
     */
    protected function getTemplate(): TemplateInterface
    {
        return $this->templateFactory->get($this->templateIdentifier, $this->templateModel)
            ->setVars($this->templateVars)
            ->setOptions($this->templateOptions);
    }

    /**
     * Get Template Type
     *
     * @param  TemplateInterface $template
     * @return string
     */
    private function getTemplateType(TemplateInterface $template): ?string
    {
        switch ($template->getType()) {
            case self::TYPE_TEXT:
                return MimeInterface::TYPE_TEXT;
            case self::TYPE_HTML:
                return MimeInterface::TYPE_HTML;
        }
        throw new InvalidArgumentException('Unknown template type');
    }

    /**
     * Prepare message.
     *
     * @return $this
     */
    protected function prepareMessage(): TransportBuilder
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();

        $mimePart = $this->mimePartInterfaceFactory->create(
            ['content' => $content, 'type' => $this->getTemplateType($template)]
        );
        $parts = count($this->attachments) ? array_merge([$mimePart], $this->attachments) : [$mimePart];
        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $parts]
        );

        $this->messageData['subject'] = $this->escaper->escapeHtml($template->getSubject());
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);

        return $this;
    }

    /**
     * Handles possible incoming types of email (string or array)
     *
     * @param  string       $addressType
     * @param  string|array $email
     * @param  string|null  $name
     * @return void
     */
    private function addAddressByType(string $addressType, $email, ?string $name = null): void
    {
        try {
            if (is_string($email)) {
                $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            }
            $convertedAddressArray = $this->addressConverter->convertMany([$email]);
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType] ?? [],
                $convertedAddressArray
            );
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__($e->getMessage()), $e);
        }
    }
}

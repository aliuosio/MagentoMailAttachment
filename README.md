# MailWithAttachment Module

## Overview

The `MailWithAttachment` module is designed to facilitate sending emails with attachments in a magento application.

## Features

- Send emails with attachments
- Supports multiple attachment types

## Installation

To install the `MagentoMailhAttachment` module, use Composer:
    
    composer require osio/magento-mail_attachment
    bin/magento setup:upgrade

## Usage

Here is a basic example of how to use the `MagentoMailhAttachment` module to send an email with an attachment:

    <?php
    
    use Osio\MagentoMailAttachment\Model\TransportBuilder;
    use Magento\Framework\Mail\Template\FactoryInterface;
    use Magento\Framework\Mail\Template\SenderResolverInterface;
    use Magento\Framework\ObjectManagerInterface;
    use Laminas\Mime\PartFactory;
    use Magento\Framework\Escaper;
    use Magento\Framework\Mail\TransportInterfaceFactory;
    use Magento\Framework\Mail\MessageInterfaceFactory;
    use Magento\Framework\Mail\EmailMessageInterfaceFactory;
    use Magento\Framework\Mail\MimeMessageInterfaceFactory;
    use Magento\Framework\Mail\MimePartInterfaceFactory;
    use Magento\Framework\Mail\AddressConverter;
    
    class EmailSender
    {
        private TransportBuilder $transportBuilder;
    
        public function __construct(
            Escaper $escaper,
            FactoryInterface $templateFactory,
            SenderResolverInterface $senderResolver,
            ObjectManagerInterface $objectManager,
            PartFactory $partFactory,
            TransportInterfaceFactory $mailTransportFactory,
            MessageInterfaceFactory $messageFactory,
            EmailMessageInterfaceFactory $emailMessageInterfaceFactory,
            MimeMessageInterfaceFactory $mimeMessageInterfaceFactory,
            MimePartInterfaceFactory $mimePartInterfaceFactory,
            AddressConverter $addressConverter
        ) {
            $this->transportBuilder = new TransportBuilder(
                $escaper,
                $templateFactory,
                $messageFactory->create(),
                $senderResolver,
                $objectManager,
                $partFactory,
                $mailTransportFactory,
                $messageFactory,
                $emailMessageInterfaceFactory,
                $mimeMessageInterfaceFactory,
                $mimePartInterfaceFactory,
                $addressConverter
            );
        }
    
        public function sendEmail()
        {
            $this->transportBuilder
                ->setTemplateIdentifier('email_template_identifier')
                ->setTemplateVars(['var1' => 'value1', 'var2' => 'value2'])
                ->setTemplateOptions(['area' => 'frontend', 'store' => 1])
                ->setFromByScope('general')
                ->addTo('recipient@example.com', 'Recipient Name')
                ->addCc('cc@example.com', 'CC Name')
                ->addBcc('bcc@example.com')
                ->setReplyTo('replyto@example.com', 'ReplyTo Name')
                ->addAttachment('Attachment content', 'attachment.txt', 'text/plain');
    
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
    }

### Contributions are welcome!

Please submit a pull request or open an issue to discuss your changes.

## License

This module is open-source and licensed under the MIT License.

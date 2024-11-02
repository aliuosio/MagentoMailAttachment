<?php declare(strict_types=1);

/**
 * @author    Osiozekhai Aliu
 * @package   Osio_MagentoMailAttachment
 * @copyright Copyright (c) 2024 Osio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osio\MagentoMailAttachment\Test\Unit\Model;

use Laminas\Mime\PartFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Osio\MagentoMailAttachment\Model\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TransportBuilderTest extends TestCase
{

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $escaper = $this->createMock(Escaper::class);
        $templateFactory = $this->createMock(FactoryInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $senderResolver = $this->createMock(SenderResolverInterface::class);
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $partFactory = $this->createMock(PartFactory::class);
        $mailTransportFactory = $this->createMock(TransportInterfaceFactory::class);
        $messageFactory = $this->createMock(MessageInterfaceFactory::class);
        $emailMessageInterfaceFactory = $this->createMock(EmailMessageInterfaceFactory::class);
        $mimeMessageInterfaceFactory = $this->createMock(MimeMessageInterfaceFactory::class);
        $mimePartInterfaceFactory = $this->createMock(MimePartInterfaceFactory::class);
        $addressConverter = $this->createMock(AddressConverter::class);

        $this->transportBuilder = new TransportBuilder(
            $escaper,
            $templateFactory,
            $message,
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

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testAddCcAddsCcAddress()
    {
        $this->transportBuilder->addCc('cc@example.com', 'CC User');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertContains('cc@example.com', $message->getCc());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testAddToAddsToAddress()
    {
        $this->transportBuilder->addTo('to@example.com', 'To User');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertContains('to@example.com', $message->getTo());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testAddBccAddsBccAddress()
    {
        $this->transportBuilder->addBcc('bcc@example.com');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertContains('bcc@example.com', $message->getBcc());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testSetReplyToSetsReplyToAddress()
    {
        $this->transportBuilder->setReplyTo('replyto@example.com', 'ReplyTo User');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertEquals('replyto@example.com', $message->getReplyTo());
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    public function testSetFromByScopeSetsFromAddress()
    {
        $this->transportBuilder->setFromByScope('from@example.com');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertEquals('from@example.com', $message->getFrom());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testAddAttachmentAddsAttachment()
    {
        $this->transportBuilder->addAttachment('content', 'file.txt', 'text/plain');
        /** @var  EmailMessageInterface $message */
        $message = $this->transportBuilder->getTransport()->getMessage();
        $this->assertCount(1, $message->getAttachments());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetTransportPreparesMessage()
    {
        $this->transportBuilder->setTemplateIdentifier('template_id')
            ->setTemplateVars(['var' => 'value'])
            ->setTemplateOptions(['option' => 'value']);

        $transport = $this->transportBuilder->getTransport();
        $this->assertInstanceOf(TransportInterface::class, $transport);
    }
}

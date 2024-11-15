<?php declare(strict_types=1);
/**
 * @author    Osiozekhai Aliu
 * @package   Osio_MagentoMailAttachment
 * @copyright Copyright (c) 2024 Osio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osio\MagentoMailAttachment\Console;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Osio\MagentoMailAttachment\Model\TransportBuilderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mail extends Command
{

    /**
     * @var TransportBuilderFactory $transportBuilderFactory
     */
    private TransportBuilderFactory $transportBuilderFactory;

    /**
     * @var State $appState
     */
    private State $appState;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     * @param State $appState
     * @param string|null $name
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory,
        State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->transportBuilderFactory = $transportBuilderFactory;
        $this->appState = $appState;
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('mail:tester')
            ->setDescription('Mail with Attachment Example');

        parent::configure();
    }

    /**
     * Execute coamnnd
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->sendEmail();
            $output->writeln('<info>Email sent successfully.</info>');
            return Command::SUCCESS;
        } catch (MailException | LocalizedException $e) {
            $output->writeln('<error>Error sending email: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    /**
     *  Send Email
     *
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    private function sendEmail()
    {
        $this->appState->setAreaCode(Area::AREA_FRONTEND);

        $transportBuilder = $this->transportBuilderFactory->create();

        $transportBuilder
            ->setTemplateIdentifier('test')
            ->setTemplateVars(['var1' => 'value1', 'var2' => 'value2'])
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => 1])
            ->setFromByScope('general')
            ->addTo('recipient@example.com', 'Recipient Name')
            ->addCc('cc@example.com', 'CC Name')
            ->addBcc('bcc@example.com')
            ->setReplyTo('replyto@example.com', 'ReplyTo Name')
            ->addAttachment('Attachment content', 'attachment.txt', 'text/plain');

        $transport = $transportBuilder->getTransport();
        $transport->sendMessage();
    }
}

<?php declare(strict_types=1);
/**
 * @author    Osiozekhai Aliu
 * @package   Osio_MaillWithAttachment
 * @copyright Copyright (c) 2024 Osio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osio\MaillWithAttachment\Test\Integration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use Osio\MaillWithAttachment\Model\TransportBuilder;
use PHPUnit\Framework\TestCase;

class TransportBuilderTest extends TestCase
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    protected function setUp(): void
    {
        $this->transportBuilder = Bootstrap::getObjectManager()->create(TransportBuilder::class);
    }

    /**
     * @throws LocalizedException
     */
    public function testAddCc()
    {
        $this->transportBuilder->addCc('test@example.com', 'Test User');
        $transport = $this->transportBuilder->getTransport();
        $this->assertNotEmpty($transport);
    }

    /**
     * @throws LocalizedException
     */
    public function testAddAttachment()
    {
        $this->transportBuilder->addAttachment('content', 'test.txt', 'text/plain');
        $transport = $this->transportBuilder->getTransport();
        $this->assertNotEmpty($transport);
    }
}

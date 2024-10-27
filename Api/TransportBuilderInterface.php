<?php declare(strict_types=1);
/**
 * Interface for TransportBuilder
 *
 * @package    Osio_MaillWithAttachment
 * @subpackage Api
 * @category   Email
 * @version    1.0.0
 * @since      2024
 *
 * @license For the full copyright and license information, please view the LICENSE
 *            file that was distributed with this source code.
 */

namespace Osio\MaillWithAttachment\Api;

use Magento\Framework\Mail\TransportInterface;

interface TransportBuilderInterface
{
    /**
     * Add Cc address
     *
     * @param  string $address
     * @param  string $name
     * @return self
     */
    public function addCc(string $address, string $name = ''): self;

    /**
     * Add To address
     *
     * @param  string $address
     * @param  string $name
     * @return self
     */
    public function addTo(string $address, string $name = ''): self;

    /**
     * Add Bcc address
     *
     * @param  string $address
     * @return self
     */
    public function addBcc(string $address): self;

    /**
     * Set Reply-To address
     *
     * @param  string      $email
     * @param  string|null $name
     * @return self
     */
    public function setReplyTo(string $email, ?string $name = null): self;

    /**
     * Set From address
     *
     * @param  string|array $from
     * @return self|null
     */
    public function setFrom($from): ?self;

    /**
     * Set From address by scope
     *
     * @param  string|array $from
     * @param  int|null     $scopeId
     * @return self
     */
    public function setFromByScope($from, ?int $scopeId = null): self;

    /**
     * Set template identifier
     *
     * @param  string $templateIdentifier
     * @return self
     */
    public function setTemplateIdentifier(string $templateIdentifier): self;

    /**
     * Set template model
     *
     * @param  string $templateModel
     * @return self
     */
    public function setTemplateModel(string $templateModel): self;

    /**
     * Set template variables
     *
     * @param  array $templateVars
     * @return self
     */
    public function setTemplateVars(array $templateVars): self;

    /**
     * Set template options
     *
     * @param  array $templateOptions
     * @return self
     */
    public function setTemplateOptions(array $templateOptions): self;

    /**
     * Get mail transport
     *
     * @return TransportInterface
     */
    public function getTransport(): TransportInterface;

    /**
     * Add attachment
     *
     * @param  string|null $content
     * @param  string|null $fileName
     * @param  string|null $fileType
     * @return self
     */
    public function addAttachment(?string $content, ?string $fileName, ?string $fileType): self;
}

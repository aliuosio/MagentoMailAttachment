```markdown
# MailWithAttachment Module

## Overview
The `MailWithAttachment` module is designed to facilitate sending emails with attachments in a PHP application. This module leverages Composer for dependency management and integrates with various email services.

## Features
- Send emails with attachments
- Supports multiple attachment types
- Configurable email settings

## Installation
To install the `MailWithAttachment` module, use Composer:

```bash
composer require osio/mail-with-attachment
```

## Usage
### Basic Example
Here is a basic example of how to use the `MailWithAttachment` module to send an email with an attachment:

```php
use Osio\MailWithAttachment\MailSender;

// Create a new instance of MailSender
$mailSender = new MailSender();

// Set email parameters
$mailSender->setRecipient('recipient@example.com');
$mailSender->setSubject('Subject of the email');
$mailSender->setBody('Body of the email');

// Add an attachment
$mailSender->addAttachment('/path/to/attachment.pdf');

// Send the email
$mailSender->send();
```

## Configuration
The module can be configured using a configuration file. Here is an example configuration:

```php
return [
    'smtp' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'your_username',
        'password' => 'your_password',
        'encryption' => 'tls',
    ],
    'from' => [
        'email' => 'no-reply@example.com',
        'name' => 'Example App',
    ],
];
```

## Contributing
Contributions are welcome! Please submit a pull request or open an issue to discuss your changes.

## License
This module is open-source and licensed under the MIT License.
```

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
    bin/magento cache:flush

## Usage

the class `Osio\MagentoMailAttachment\Console` is a basic example  usage
how to use the `MagentoMailhAttachment` module to send an email with an attachment:
You can test the Console command with `bin/magento mail:tester `
>  use a mail cacher like [Mailhog](https://github.com/mailhog/MailHog) to catch the E-Mails on yout locahost


## Contributions are welcome!
Please submit a pull request or open an issue to discuss your changes.

##  License
This module is open-source and licensed under the MIT License.

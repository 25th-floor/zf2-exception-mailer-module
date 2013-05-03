# ZendFramework 2 Exception Mailer Module

A simple ZF2 Module for sending Mails if Exceptions happen on production systems. In it's simplest configuration it
just sends the stack trace of the Exception. But you can also render views and send html mails instead.

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
php composer.phar require 25th/zf2-exception-mailer-module
# (When asked for a version, type `0.*`)
```

Then add `ExceptionMailer` to your `config/application.config.php`.

Installation without composer is not officially supported and requires you to manually install all dependencies
that are listed in `composer.json`


## Configuration

To configure the Mailer use your application config:
```php
<?php
return array(
	// Exception Stuff
	'exception_mailer' => array(
		// Mail
		'send' => true,
		'sender' => 'your-sender-address@mail.com',
		'recipients' => array(
			'your-recipient-address@mail.com',
		),
		'subject' => 'My Exception Mailer',

		// HTML Templates
		'useTemplate' => false,
		'template' => 'error/index'
	),
);
```

### HTML Emails

For HTML Emails set useTemplate to true and use the template parameter for your template configuration.

If you want to use a different template f.e. "error/mail" you also have to define it in the
view_manager => template_map array with the correct position to it

```php
<?php
return array(
	...
	'view_manager' => array(
		'template_map'        => array(
			...
			'error/mail'              => __DIR__ . '/../view/error/mail.phtml', // Exception_Mailer
			...
		),
	),
	...
);
```

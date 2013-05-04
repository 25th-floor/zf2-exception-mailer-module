<?php
return array(
	// Exception Stuff
	'exception_mailer' => array(
		// Mail
		'send' => false,
		'sender' => 'your@mail.com',
		'recipients' => array(
//			'your@mail.com',
		),
		'subject' => 'My Exception Mailer',

		// HTML Templates
		'useTemplate' => false,
		'template' => 'error/index'
	),
);
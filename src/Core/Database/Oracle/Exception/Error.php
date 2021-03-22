<?php
namespace Core\Database\Oracle\Exception;

class Error extends \Core\Exception\Error
{
	const MESSAGE_TEMPLATE = "Error code: %s, Error message: %s";

	public function __construct($message)
	{
		parent::__construct(sprintf(self::MESSAGE_TEMPLATE, $message['code'], $message['message']));
	}
}
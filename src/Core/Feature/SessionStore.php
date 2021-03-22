<?php
namespace Core\Feature;

interface SessionStore
{
	public function __sleep();
	public function __wakeup();
}
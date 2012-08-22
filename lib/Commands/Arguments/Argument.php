<?php
namespace Phud\Commands\Arguments;

abstract class Argument
{
	const STATUS_VALID = 'valid';
	const STATUS_INVALID = 'invalid';

	protected $required = true;

	abstract public function parse($arg);

	public function setNotRequired()
	{
		$this->required = false;
		return $this;
	}

	public function fail($message)
	{
		if($this->required) {
			throw new \InvalidArgumentException($message);
		}
	}

	public function __toString()
	{
		return get_class();
	}
}

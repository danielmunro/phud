<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	\InvalidArgumentException;

abstract class Argument
{
	const STATUS_VALID = 'valid';
	const STATUS_INVALID = 'invalid';

	protected $required = true;
	protected $status = self::STATUS_VALID;

	public function parse(aActor $actor, $arg)
	{
		$result = $this->parseArg($actor, $arg);
		if($this->status === self::STATUS_INVALID && $this->required) {
			throw new InvalidArgumentException('Argument required and is not valid: '.$arg);
		}
		return $result;
	}

	abstract protected function parseArg(aActor $actor, $arg);

	public function setNotRequired()
	{
		$this->required = false;
	}
}

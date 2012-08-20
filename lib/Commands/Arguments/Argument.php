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
	protected $min_argument_count = 0;
	protected $min_argument_fail = "What were you trying to do?";

	public function parse(aActor $actor, $arg)
	{
		if($this->min_argument_count && sizeof($arg) - 1 <= $this->min_argument_count) {
			$actor->notify($this->min_argument_fail);
			throw new InvalidArgumentException();
		}
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
		return $this;
	}

	public function fail(aActor $actor, $message)
	{
		$this->status = self::STATUS_INVALID;
		if($this->required) {
			$actor->notify($message);
		}
	}

	public function __toString()
	{
		return get_class();
	}
}
<?php
namespace Phud\Commands;
use Phud\Actors\User,
	Phud\Instantiate,
	\Exception,
	\InvalidArgumentException,
	Phud\Alias,
	Phud\Actors\Actor;

abstract class Command
{
	use Alias, Instantiate;

	protected $alias = null;
	protected $dispositions = [];
	
	protected function __construct()
	{
		$this->setupAliases($this->alias);
	}

	//abstract protected function getArgumentHints();

	protected function setupAliases($alias)
	{
		if(is_array($alias)) {
			if(is_numeric($alias[1])) {
				list($alias, $priority) = $alias;
				self::addAlias($alias, $this, $priority);
			} else {
				foreach($alias as $a) {
					$this->setupAliases($a);
				}
			}
		} else if(is_string($alias)) {
			self::addAlias($alias, $this);
		} else {
			throw new Exception(get_class($this).' is not fully configured.');
		}
	}

	public function getDispositions()
	{
		return $this->dispositions;
	}

	public function tryPerform(Actor $actor, $args = [])
	{
		$fail = false;

		if($this instanceof DM && !$actor->isDM()) {
			$fail = "You cannot do that.";
		} else if(!in_array($actor->getDisposition(), $this->dispositions)) {
			if($actor->getDisposition() === Actor::DISPOSITION_SITTING) {
				$fail = "You need to stand up.";
			} else if($actor->getDisposition() === Actor::DISPOSITION_SLEEPING) {
				$fail = "You are asleep!";
			}
		}
		
		if($fail) {
			return $actor->notify($fail);
		}
		/**
		try {
			$args = $this->getArgumentsFromHints($actor, $args);
		} catch (InvalidArgumentException $e) {
			return;
		}
		*/
		$args = array_merge($args, $this->getArgumentHints());
		//call_user_func_array([$this, 'perform'], $args);
		$this->perform($actor, $args, $this->getArgumentHints());
	}

/**
	public function getArgumentsFromHints(Actor $actor, $args)
	{
		$argument_hints = $this->getArgumentHints();
		$command_args = [];
		while($arg = array_pop($args)) {
			$hint = array_pop($argument_hints);
			$command_args[] = $hint->parse($actor, $arg);
		}
		foreach($command_args as $a) {
			echo $a."\n";
		}
		return $command_args;
	}
	*/
}

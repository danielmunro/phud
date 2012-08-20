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
	protected $min_argument_count = 0;
	protected $min_argument_fail = "What were you trying to do?";
	
	protected function __construct()
	{
		$this->setupAliases($this->alias);
	}

	protected function getArgumentsFromHints()
	{
		return [];
	}

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
		if($this->min_argument_count && sizeof($args) - 2 <= $this->min_argument_count) {
			return $actor->notify($this->min_argument_fail);
		}

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

		try {
			$found_args = $this->getArgumentsFromHints($actor, $args);
			if(!is_array($found_args)) {
				throw new Exception($this.' command misconfigured. getArgumentsFromHints() does not return an array.');
			}
			array_unshift($found_args, $actor);
			call_user_func_array([$this, 'perform'], $found_args);
		} catch(InvalidArgumentException $e) {
			return;
		}
	}
}

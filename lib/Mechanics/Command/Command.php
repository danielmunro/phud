<?php
namespace Mechanics\Command;
use \Living\User,
	\Mechanics\Debug,
	\Mechanics\Server,
	\ReflectionClass,
	\Mechanics\Alias,
	\Mechanics\Actor;

abstract class Command
{
	use Alias;

	protected $dispositions = [];
	
	protected function __construct() {}
	
	public static function runInstantiation()
	{
		$namespace = 'Commands';
		$d = dir(dirname(__FILE__) . '/../../'.$namespace);
		while($command = $d->read()) {
			if(substr($command, -4) === ".php") {
				Debug::addDebugLine("init command: ".$command);
				$class = substr($command, 0, strpos($command, '.'));
				$called_class = $namespace.'\\'.$class;
				$reflection = new ReflectionClass($called_class);
				if(!$reflection->isAbstract()) {
					new $called_class();
				}
			}
		}
	}

	public function getDispositions()
	{
		return $this->dispositions;
	}
	
	public function hasArgCount(Actor $actor, $args, $count)
	{
		if(sizeof($args) < $count)
		{
			Server::out($actor, "Not enough args.");
			return false;
		}
		return true;
	}

	public function tryPerform(User $user, $args = [], $command_subscriber)
	{
		if($this instanceof DM && !$user->isDM())
			return Server::out($user, "You cannot do that.");
		else if(!in_array($user->getDisposition(), $this->dispositions)) {
			if($user->getDisposition() === Actor::DISPOSITION_SITTING)
				return Server::out($user, "You need to stand up.");
			else if($user->getDisposition() === Actor::DISPOSITION_SLEEPING)
				return Server::out($user, "You are asleep!");
		}
		
		$this->perform($user, $args, $command_subscriber);
	}
}
?>

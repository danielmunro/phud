<?php
namespace Phud\Commands;
use Phud\Actors\User,
	Phud\Debug,
	Phud\Server,
	\ReflectionClass,
	\Exception,
	Phud\Alias,
	Phud\Actors\Actor;

abstract class Command
{
	use Alias;

	protected $alias = null;
	protected $dispositions = [];
	
	protected function __construct()
	{
		if(is_array($this->alias)) {
			list($alias, $priority) = $this->alias;
			self::addAlias($alias, $this, $priority);
		} else if(is_string($this->alias)) {
			self::addAlias($this->alias, $this);
		} else {
			throw new Exception(get_class($this).' is not fully configured.');
		}
	}
	
	public static function runInstantiation()
	{
		global $global_path;
		$namespace = 'Commands';
		$d = dir($global_path.'/lib/'.$namespace);
		while($command = $d->read()) {
			if(substr($command, -4) === ".php") {
				Debug::log("init command: ".$command);
				$class = substr($command, 0, strpos($command, '.'));
				$called_class = 'Phud\\'.$namespace.'\\'.$class;
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

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
		$this->setupAliases($this->alias);
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
	
	public static function runInstantiation()
	{
		global $global_path;
		$d = dir($global_path.'/deploy/init/Commands');
		while($command = $d->read()) {
			if(substr($command, -4) === ".php") {
				Debug::log("init command: ".$command);
				$class = substr($command, 0, strpos($command, '.'));
				$called_class = 'Phud\\Commands\\'.$class;
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

	public function tryPerform(User $user, $args = [])
	{
		if($this instanceof DM && !$user->isDM())
			return Server::out($user, "You cannot do that.");
		else if(!in_array($user->getDisposition(), $this->dispositions)) {
			if($user->getDisposition() === Actor::DISPOSITION_SITTING)
				return Server::out($user, "You need to stand up.");
			else if($user->getDisposition() === Actor::DISPOSITION_SLEEPING)
				return Server::out($user, "You are asleep!");
		}
		
		$this->perform($user, $args);
	}
}
?>

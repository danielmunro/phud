<?php
namespace Phud\Commands;
use Phud\Actors\User,
	Phud\Debug,
	Phud\Server,
	Phud\Instantiate,
	\Exception,
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

Server::instance()->on('initialized', function() {
	Command::init();
});
?>

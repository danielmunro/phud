<?php
namespace Phud\Quests;
use Phud\Actors\User,
	Phud\Debug,
	Phud\Interactive,
	Phud\Identity,
	Phud\Instantiate,
	Phud\Server,
	\Exception,
	\ReflectionClass;

abstract class Quest
{
	use Identity, Interactive, Instantiate;

	protected $requirements_to_accept = null;
	protected $reward = null;
	protected $status = 'initialized';

	const STATUS_INITIALIZED = 'initialized';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CLOSED = 'closed';
	
	public function __construct()
	{
		if(!isset($this->id)) {
			throw new Exception('Quest error: '.$this.' needs an identifier');
		}
		self::$identities[$this->id] = $this;
	}

	abstract public function getListeners();

	abstract public function reward(User $user);

	abstract public function canAccept(User $user);

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function __toString()
	{
		return $this->short;
	}
}

Server::instance()->on('initialized', function() {
	Quest::init();
});
?>

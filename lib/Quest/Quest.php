<?php
namespace Phud\Quest;
use Phud\Actors\User,
	Phud\Nouns,
	Phud\Identity;

class Quest
{
	use Nouns, Identity;

	protected $short = '';
	protected $long = '';
	protected $requirements_to_accept = null;
	protected $subscribers = [];
	protected $reward = null;
	protected $status = 'initialized';

	const STATUS_INITIALIZED = 'initialized';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CLOSED = 'closed';
	
	public function __construct()
	{
		if(!is_callable($this->requirements_to_accept) || !is_callable($this->reward)) {
			throw new Exception('quest not configured correctly');
		}
		self::$identities[$this->id] = $this;
	}

	public function canAccept(User $user)
	{
		return call_user_func_array($this->requirements_to_accept, [$user]);
	}

	public function applySubscribers(User $user)
	{
		foreach($this->subscribers as $subscriber) {
			$user->addSubscriber($subscriber);
		}
	}

	public function reward(User $user)
	{
		return call_user_func_array($this->reward, [$user]);
	}

	public function getShort()
	{
		return $this->short;
	}
	public function getLong()
	{
		return $this->long;
	}

	public function getInitializingProperties()
	{
		return $this->initializing_properties;
	}

	public function getDefaultNouns()
	{
		return $this->short;
	}

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

	public function __sleep()
	{
		return ['id', 'status'];
	}

	public function __wakeup()
	{
		$q = self::getByID($this->id);
		foreach($q->getInitializingProperties() as $p => $v) {
			if($p === 'status') {
				continue;
			}
			$this->$p = $v;
		}
	}
}
?>

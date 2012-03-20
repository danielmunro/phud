<?php
namespace Mechanics\Quest;
use \Living\User as User,
	\Mechanics\Nouns,
	\Mechanics\EasyInit,
	\Mechanics\Identity;

class Quest
{
	use Nouns, EasyInit, Identity;

	protected $short = '';
	protected $long = '';
	protected $requirements_to_accept = null;
	protected $subscribers = [];
	protected $reward = null;
	protected $satisfied = false;
	protected $initializing_properties = [];
	
	public function __construct($properties = [])
	{
		$this->initializing_properties = $properties;
		$this->initializeProperties($properties);
		if(!is_callable($this->requirements_to_accept) || !is_callable($this->reward)) {
			throw new Exception('quest not configured correctly');
		}
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
		return $this->reward($user);
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

	public function __toString()
	{
		return $this->short;
	}

	public function __sleep()
	{
		return ['id'];
	}

	public function __wakeup()
	{
		$q = self::getByID($this->id);
		foreach($q->getInitializingProperties() as $p => $v) {
			$this->$p = $v;
		}
	}
}
?>

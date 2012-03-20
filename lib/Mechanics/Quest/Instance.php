<?php
namespace Mechanics\Quest;
use \Living\User,
	\Mechanics\Actor;
class Instance
{
	protected $quest = null;
	protected $actor = null;
	protected $requirements_progress = null;
	
	public function __construct(Actor $actor, Quest $quest)
	{
		$this->actor = $actor;
		$this->quest = $quest;

		if($this->actor instanceof User) {
			$this->quest->applySubscribers($actor);
		}
	}
	
	public function getActor()
	{
		return $this->actor;
	}
	
	public function getQuest()
	{
		return $this->quest;
	}
}
?>

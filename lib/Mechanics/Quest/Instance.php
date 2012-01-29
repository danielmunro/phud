<?php
namespace Mechanics\Quest;
use \Living\User;
use \Mechanics\Actor;
class Instance
{
	protected $quest = null;
	protected $actor = null;
	protected $requirements_progress = null;
	
	public function __construct(Actor $actor, Quest $quest)
	{
		$this->actor = $actor;
		$this->quest = $quest;
		$this->requirements_progress = new Requirements($this->quest->getRequirementsToComplete());
	}
	
	public function getActor()
	{
		return $this->actor;
	}
	
	public function getQuest()
	{
		return $this->quest;
	}
	
	public function getRequirementsProgress()
	{
		return $this->requirements_progress;
	}
	
	public function isQualifiedToComplete()
	{
		return $this->quest->isQualifiedToComplete($this->actor);
	}

	public function getExperience()
	{
		$quest_level = $this->quest->getRequirementsToAccept()->getLevel();
		$actor_level = $this->actor->getLevel();
		$mod = 0.1 * ($actor_level - $quest_level);
		return $this->quest->getExperience() * $mod;
	}
}
?>

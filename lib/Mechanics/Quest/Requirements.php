<?php
namespace Mechanics\Quest;
use \Living\User,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Item,
	\Mechanics\Usable,
	\Mechanics\Inventory;

class Requirements
{
	use Inventory, Usable;

	protected $races = [];
	protected $level = 0;
	protected $previous_quests = [];
	
	public function __construct(Requirements $requirements = null)
	{
		if($requirements)
		{
			$this->races = $requirements->getRaces();
			$this->level = $requirements->getLevel();
			$this->items = $requirements->getItems();
			$this->previous_quests = $requirements->getPreviousQuests();
			return;
		}
	}
	
	public function getRaces()
	{
		return $this->races;
	}
	
	public function getLevel()
	{
		return $this->level;
	}
	
	public function getPreviousQuests()
	{
		return $this->previous_quests;
	}
	
	public function isQualified(User $user, Questmaster $questmaster = null)
	{
		$say = Alias::lookup('say');
		
		if($user->getLevel() < $this->level)
		{
			if($questmaster)
				$say->perform($questmaster, "You're too inexperienced for this quest");
			return false;
		}
		if((empty($this->races) || in_array($user->getRace(), $this->races)))
		{
			if(sizeof($this->getItems()))
			{
				$missing = array_diff($this->getItems(), $user->getItems());
				if($missing)
				{
					if($questmaster)
						$say->perform($questmaster, "You are missing these items: ".implode(", ", $missing));
					return false;
				}
			}
			if(sizeof($this->previous_quests))
			{
				$quests = $this->previous_quests;
				foreach($user->getQuestLog()->getQuests() as $quest_instance)
				{
					$key = array_search($quest_instance->getQuest(), $this->previous_quests);
					if($key !== false)
						array_splice($quests, $key, 1);
				}
				if($quests)
				{
					if($questmaster)
						$say->perform($questmaster, "You are missing these quests: ".implode(", ", $quests));
					return false;
				}
			}
			return true;
		}
		return false;
	}
}
?>

<?php
namespace Phud\Quest;
use Phud\Usable;

trait Log
{
	use Usable;

	protected $quests = [];

	public function addQuest(Quest $quest)
	{
		$this->quests[$quest->getID()] = new Quest($quest->getInitializingProperties());
	}
	
	public function removeQuest(Quest $quest)
	{
		if(isset($this->quests[$quest->getID()])) {
			unset($this->quests[$quest->getID()]);
		}
	}

	public function getQuests()
	{
		return $this->quests;
	}

	public function getQuestByID($id)
	{
		return isset($this->quests[$id]) ? $this->quests[$id] : null;
	}

	public function getQuestByInput($input)
	{
		return $this->getUsableByInput($this->quests, $input);
	}
}
?>

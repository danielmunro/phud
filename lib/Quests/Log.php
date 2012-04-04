<?php
namespace Phud\Quests;
use Phud\Usable;

trait Log
{
	use Usable;

	protected $quests = [];

	public function addQuest(Quest $quest)
	{
		$this->quests[get_class($quest)] = $quest;
	}
	
	public function removeQuest(Quest $quest)
	{
		$i = get_class($quest);
		if(isset($this->quests[$i])) {
			unset($this->quests[$i]);
		}
	}

	public function getQuests()
	{
		return $this->quests;
	}

	public function getQuestByInput($input)
	{
		return $this->getUsableByInput($this->quests, $input);
	}
}
?>

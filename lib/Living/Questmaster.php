<?php
namespace Living;
use \Mechanics\Quest;
class Questmaster extends Mob
{
	protected $quests = [];
	protected $list_message = 'Here are my quests:';
	
	public function getQuests()
	{
		return $this->quests;
	}
	
	public function getListMessage()
	{
		return $this->list_message;
	}
	
	public function addQuest(Quest $quest)
	{
		$this->quests[] = $quest;
	}
	
	public function removeQuest(Quest $quest)
	{
		$key = array_search($quest, $this->quests);
		if($key !== false)
		{
			unset($this->quests[$key]);
			$this->quests = array_values($this->quests);
		}
	}
	
	public function setListMessage($list_message)
	{
		$this->list_message = $list_message;
	}
}
?>

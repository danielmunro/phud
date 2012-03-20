<?php
namespace Living;
use \Mechanics\Quest\Log as QuestLog;

class Questmaster extends Mob
{
	use QuestLog;

	protected $list_message = 'Here are my quests:';
	
	public function getListMessage()
	{
		return $this->list_message;
	}
}
?>

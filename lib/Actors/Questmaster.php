<?php
namespace Phud\Actors;
use Phud\Quest\Log as QuestLog;

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

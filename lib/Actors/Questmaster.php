<?php
namespace Phud\Actors;
use Phud\Quests\Log;

class Questmaster extends Mob
{
	use Log;

	protected $list_message = 'Here are my quests:';
	
	public function getListMessage()
	{
		return $this->list_message;
	}
}
?>

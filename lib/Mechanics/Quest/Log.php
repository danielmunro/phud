<?php
namespace Mechanics\Quest;
use \Living\User as User,
	\Mechanics\Usable;

class Log
{
	use Usable;

	protected $user = null;
	protected $quests = [];

	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	public function add(Quest $quest)
	{
		$this->quests[] = new Instance($this->user, $quest);
	}
	
	public function remove(Quest $quest)
	{
		$key = array_search($quest, $this->quests);
		if($key !== false)
			array_splice($this->quests, $key, 1);
	}

	public function getQuestByInput($input)
	{
		return $this->getUsableNounByInput($this->quests, $input);
	}
}
?>

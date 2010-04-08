<?php

	abstract class Questmaster extends Actor
	{
		protected $quest_index = 0;
		
		//public function __construct($alias, $noun, $description, $room_id, $level, $race)
		//public function __construct($room_id)
		//{
		
			//$this->alias = $alias;
			//$this->noun = $noun;
			//$this->description = $description;
			//$this->level = $level;
			//$this->setRace($race);
			//parent::__construct($room_id);
		
		//}
		
		abstract public function questInfo(&$actor);
		abstract public function questAward(&$actor);
		abstract public function questAccept(&$actor);
		abstract public function questDone(&$actor);
		
		public function getTable() { return 'quest'; }
		
	}

?>

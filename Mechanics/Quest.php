<?php
	namespace Mechanics;
	class Quest
	{
	
		private static $instances = array();
		private $id = 0;
		private $user_id = 0;
		private $quest_id = 0;
		private $points = 0;
		private $accepted = false;
		private $complete = false;
		private $award_obtained = false;
	
		public function __construct($user_id, $quest_id)
		{
		
			$this->user_id = $user_id;
			$this->quest_id = $quest_id;
		
			$row = Db::getInstance()->query('SELECT * FROM quests WHERE fk_user_id = ? AND fk_quest_id = ?', array($this->user_id, $this->quest_id))->getResult()->fetch_object();
			
			if(empty($row))
				return;
			
			$this->id = $row->id;
			$this->points = $row->points;
			$this->accepted = $row->accepted;
			$this->complete = $row->complete;
			$this->award_obtained = $row->award_obtained;
		}
	
		public static function find($user_id, $quest_id)
		{
		
			if(!isset(self::$instances[$user_id][$quest_id]))
				self::$instances[$user_id][$quest_id] = new self($user_id, $quest_id);
			
			return self::$instances[$user_id][$quest_id];
		}
		
		public function save()
		{
			
			if($this->id)
				Db::getInstance()->query('UPDATE quests SET points = ?, accepted = ?, complete = ?, award_obtained = ? WHERE id = ?', array($this->points, $this->accepted, $this->complete, $this->award_obtained, $this->id);
			else
				$this->id = Db::getInstance()->query('INSERT INTO quests (points, accepted, complete, award_obtained, fk_user_id, fk_quest_id) values (?, ?, ?, ?, ?, ?)', 
														array($this->points, $this->accepted, $this->complete, $this->award_obtained, $this->user_id, $this->quest_id))->insert_id;
		}
		
		public function getUserId() { return $this->user_id; }
		public function getQuestId() { return $this->quest_id; }
		public function getPoints() { return $this->points; }
		public function getComplete() { return $this->complete; }
		public function getAccepted() { return $this->accepted; }
		public function getAwardObtained() { return $this->award_obtained; }
		
		public function addPoint() { $this->points++; $this->save(); }
		public function setComplete($complete) { $this->complete = $complete; $this->save(); }
		public function setAccepted($accepted) { $this->accepted = $accepted; $this->save(); }
		public function setAwardObtained($award) { $this->award_obtained = $award; $this->save(); }
	}
?>

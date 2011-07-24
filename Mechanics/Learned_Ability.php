<?php

	namespace Mechanics;
	class Learned_Ability
	{
		private $actor = null;
		private $ability = null;
		private $percent = 0;
		
		public function __construct(Actor $actor, Ability $ability, $percent = 0)
		{
			$this->actor = $actor;
			$this->ability = $ability;
			$this->percent = $percent;
		}
		
		public function getAbility()
		{
			return $this->ability;
		}
		
		public function getPercent()
		{
			return $this->percent;
		}
		
		public function perform($args = array())
		{
			return $this->ability->perform($this->$actor, $args);
		}
	}

?>

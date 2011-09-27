<?php

	namespace Mechanics;
	class Learned_Ability
	{
		private $actor = null;
		private $ability = null;
		private $percent = 0;
		
		public function __construct(Ability $ability, Actor $actor = null, $percent = 0)
		{
			$this->actor = $actor;
			$this->ability = $ability->getAlias()->getAliasName();
			$this->percent = $percent;
		}
		
		public function getAbility()
		{
			return Alias::lookup($this->ability);
		}
		
		public function getPercent()
		{
			return $this->percent;
		}
		
		public function getActor()
		{
			return $this->actor;
		}
		
		public function perform($args = array())
		{
			if(!$this->actor)
				return;
			return Alias::lookup($this->ability)->perform($this->actor, $this->percent, $args);
		}
	}

?>

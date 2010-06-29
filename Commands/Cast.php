<?php
	namespace Commands;
	class Cast extends \Mechanics\Command
	{
		
		protected function __construct()
		{
			\Mechanics\Command::addAlias(__CLASS__, array('c', 'cast'));
		}
		
		public static function perform(&$actor, $args = null)
		{
			if($args[1] == 'cure')
			{
				if(isset($args[2]) && strpos($args[2], 'l') === 0)
					$spell_name = 'Cure_Light';
				else
					$spell_name = 'Cure_Light';
			}
			
			/**
			 * Find all applicable spells and perform classes
			 */
			$spell = Skill::findByAliasAndName($actor->getAlias(), $spell_name);
			$perform = Perform::find('Spell_' . $spell_name);
			if(empty($spell))
				return Server::out($actor, "You can't cast that.");
			
			/**
			 * Figure out mana cost
			 */
			$mana_cost = 50;
			
			if($actor->getLevel() > $perform->getLevel())
				$mana_cost = $perform->getModifiedManaCost($actor);
			
			if($actor->getMana() < $mana_cost)
				return Server::out($actor, "You don't have enough mana for that.");
			
			/**
			 * Concentration test
			 */
			if(rand(0, 100) > $spell->getProficiency())
			{
				$actor->setMana($actor->getMana() - ($mana_cost / 2));
				return Server::out($actor, "You lost your concentration.");	
			}
			
			/**
			 * Announce to everyone
			 */
			$actors = ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId());
			foreach($actors as $a)
				if($a->getAlias() != $actor->getAlias())
					Server::out($a, $actor->getAlias(true) . " utters the words, '" . $perform->getName($actor) . "'");
			
			/**
			 * Perform
			 */
			$perform->perform($actor, $spell, $args);
			$perform->checkGain($actor, $spell);
			$actor->setMana($actor->getMana() - $mana_cost);
		}
	}
?>

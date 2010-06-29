<?php

	class Caster_Healing extends Caster
	{
		public static function giveGroupToActor(Actor &$actor)
		{
			new Skill(0, Perform::find('Spell_Cure_Light')->getName(), 0, $actor->getAlias(), $actor->getId());
			new Skill(0, Perform::find('Spell_Cure_Serious')->getName(), 0, $actor->getAlias(), $actor->getId());
			new Skill(0, Perform::find('Spell_Cure_Critical')->getName(), 0, $actor->getAlias(), $actor->getId());
			new Skill(0, Perform::find('Spell_Heal')->getName(), 0, $actor->getAlias(), $actor->getId());
			Skill::saveSet($actor->getAlias());
		}
	}
?>

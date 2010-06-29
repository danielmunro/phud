<?php

	abstract class Caster
	{
		
		const TYPE_HEALING = 0;
		const TYPE_GEOMANCY = 1;
		const TYPE_BENEDICTIONS = 2;
		const TYPE_NECROMANCY = 3;
		const TYPE_SORCERY = 4;
		const TYPE_SUMMONING = 5;
		
		abstract public static function giveGroupToActor(Actor &$actor);
	}
?>

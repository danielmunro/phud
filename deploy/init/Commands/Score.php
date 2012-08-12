<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class Score extends User
{
	protected $alias = 'score';

	public function perform(lUser $user, $args = [])
	{
		$user->notify("You are ".$user.", a ".$user->getRace()."\n".
			"Attributes:\n".
			implode(' ', array_map(function($v) use ($user) {
				return ucfirst($v)." ".$user->getAttribute($v)." (".$user->getUnmodifiedAttribute($v).")";
			}, ['str', 'int', 'wis', 'dex', 'con', 'cha']))."\n".
			"Hp: ".$user->getAttribute('hp')."/".$user->getMaxAttribute('hp').
			" Mana: ".$user->getAttribute('mana')."/".$user->getMaxAttribute('mana').
			" Movement: ".$user->getAttribute('movement')."/".$user->getMaxAttribute('movement')."\n".
			"Trains: ".$user->getTrains().", practices: ".$user->getPractices()."\n".
			"Level ".$user->getLevel().", 1 experience to next level\n".
			$user->getCurrency('gold')." gold, ".$user->getCurrency('silver')." silver, ".$user->getCurrency('copper')." copper\n".
			"You are ".self::getAcString($user->getAttribute('ac_bash'))." against bashing\n".
			"You are ".self::getAcString($user->getAttribute('ac_slash'))." against slashing\n".
			"You are ".self::getAcString($user->getAttribute('ac_pierce'))." against piercing\n".
			"You are ".self::getAcString($user->getAttribute('ac_magic'))." against magic");
	}
	
	private static function getAcString($ac)
	{
		if($ac >= 100)
			return "hopelessly vulnerable to";
		if($ac >= 80)
			return "defenseless against";
		if($ac >= 60)
			return "barely protected from";
		if($ac >= 40)
			return "slightly armored against";
		if($ac >= 20)
			return "somewhat armored against";
		if($ac >= 0)
			return "armored against";
		if($ac >= -20)
			return "well-armored against";
		if($ac >= -40)
			return "very well-armored against";
		if($ac >= -60)
			return "heavily armored against";
		if($ac >= -80)
			return "superbly armored against";
		if($ac >= -100)
			return "almost invulnerable to";
		return "divinely armored against";
	}
}

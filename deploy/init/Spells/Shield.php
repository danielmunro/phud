<?php
namespace Spells;
use \Mechanics\Ability\Spell,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Affect,
	\Mechanics\Server;

class Shield extends Spell
{
	protected $alias = 'shield';
	protected $proficiency = 'benedictions';
	protected $required_proficiency = 25;
	protected $saving_attribute = 'wis';

	protected function success(Actor $actor)
	{
		$timeout = min(30, ceil($proficiency / 2));
		$mod_ac = min(-(round($proficiency / 1.5)), -30);
		
		$a = new Affect();
		$a->setAffect(self::$name_familiar);
		$a->setMessageAffect('Spell: shield: '.$mod_ac.' to armor class');
		$a->setMessageEnd('You feel less protected.');
		$a->setTimeout($timeout);
		$atts = $a->getAttributes();
		$atts->setAcBash($mod_ac);
		$atts->setAcSlash($mod_ac);
		$atts->setAcPierce($mod_ac);
		$atts->setAcMagic($mod_ac);
		$a->apply($target);
		Server::out($target, "You feel more protected!");
		return false;
	}
}
?>

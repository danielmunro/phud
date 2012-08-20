<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Acolyte;

class Practice extends User
{
	protected $alias = 'practice';
	protected $disposition = ['standing'];

	public function perform(aUser $user, Acolyte $acolyte = null, $proficiency = '')
	{
		// Get a list of things to practice
		if(empty($acolyte)) {
			$out = '';
			foreach($user->getProficiencies() as $proficiency) {
				$spacer = str_pad('', 30 - strlen($proficiency));
				$out .= $proficiency.$spacer.$proficiency->getScore()."\n";
			}
			return $user->notify("Practice list:\n".$out);
		}
		// practice a proficiency
		else {
			$acolyte->practice($user, $proficiency);
		}
	}
	
	protected function getArgumentsFromHints($user, $args)
	{
		$s = sizeof($args);
		if($s === 1) {
			return [];
		}
		// user wants to practice -- look for an acolyte in the room
		$acolyte = null;
		foreach($user->getRoom()->getActors() as $a) {
			if($a instanceof Acolyte) {
				$acolyte = $a;
			}
		}
		if(empty($acolyte)) {
			$user->notify("No one can help you practice.");
			throw new \InvalidArgumentException();
		}
		return [$acolyte, recombine($args, 1, $s-1)];
	}
}

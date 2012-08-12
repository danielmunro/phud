<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Acolyte;

class Practice extends User
{
	protected $alias = 'practice';
	protected $disposition = ['standing'];

	public function perform(aUser $user, $args = [])
	{
		// Get a list of things to practice
		if(!isset($args[1])) {
			$out = '';
			foreach($user->getProficiencies() as $proficiency) {
				$spacer = str_pad('', 30 - strlen($proficiency));
				$out .= $proficiency.$spacer.$proficiency->getScore()."\n";
			}
			return $user->notify("Practice list:\n".$out);
		}

		// practice a proficiency
		else {
			foreach($user->getRoom()->getActors() as $a) {
				if($a instanceof Acolyte) {
					$a->practice($user, $args[1]);
				}
			}
		}
	}
}

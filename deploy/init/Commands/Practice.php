<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Acolyte,
	Phud\Server;

class Practice extends User
{
	protected $alias = 'practice';
	protected $disposition = ['standing'];

	public function perform(aUser $user, $args = [])
	{
		// Get a list of things to practice
		if(!isset($args[1])) {
			$out = '';
			foreach($user->getProficiencies() as $proficiency_name => $score) {
				$spacer = str_pad('', 30 - strlen($proficiency_name));
				$out .= $proficiency_name.$spacer.$score."\n";
			}
			return Server::out($user, "Practice list:\n".$out);
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
?>

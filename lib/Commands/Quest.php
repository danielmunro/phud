<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Actors\Questmaster,
	Phud\Actors\User as lUser;

class Quest extends User
{
	protected $alias = 'quest';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(lUser $user, $args = [])
	{
		if(empty($args[1])) {
			$this->listCurrentQuests($user);
		} else if($args[1] === 'list') {
			$this->doList($user, $args);
		} else if($args[1] === 'accept') {
			$this->doAccept($user, $args);
		} else if($args[1] === 'finish') {
			$this->doFinish($user, $args);
		}
	}

	private function listCurrentQuests(lUser $user)
	{
		$msg = ['initialized' => '', 'completed' => '', 'closed' => ''];
		foreach($user->getQuests() as $quest) {
			$msg[$quest->getStatus()] .= '['.$quest->getStatus().'] '.$quest."\n";
		}

		Server::out($user, "Active Quests:\n\n".$msg['initialized'].
			"\nCompleted Quests:\n\n".$msg['completed']);
	}

	private function doFinish(lUser $user, $args = [])
	{
		$questmaster = $this->findQuestmaster($user);
		if($questmaster) {
			$quest = $questmaster->getQuestByInput(array_pop($args));
			if($quest) {
				$user->finishQuest($quest);
				return Server::out($user, "You have finished the quest ".$quest.".");
			}
		}
		return Server::out($user, "Which quest would you like to finish?");
	}
	
	private function doList(lUser $user, $args = [])
	{
		$questmaster = $this->findQuestmaster($user, $args);
		if($questmaster) {
			Server::out($user, $questmaster->getListMessage());
			foreach($questmaster->getQuests() as $quest) {
				if(!$user->hasCompletedQuest($quest)) {
					Server::out($user, 
						'['.$quest.']'.($quest->canAccept($user) ? '' : ' (unavailable)')."\n".
						$quest->getLong());
				}
			}
		} else {
			return $this->listCurrentQuests($user);
		}
	}

	private function doAccept(lUser $user, $args = [])
	{
		$questmaster = $this->findQuestmaster($user);
		if($questmaster) {
			$quest = $questmaster->getQuestByInput(array_pop($args));
			if($quest) {
				$user->addQuest($quest);
				return Server::out($user, "You accept the quest ".$quest.".");
			}
		}
		return Server($user, "There is no quest to accept.");
	}

	private function findQuestmaster(lUser $user, $args = [])
	{
		$questmaster = null;
		if(sizeof($args) > 2) {
			$lookup = array_pop($args);
			$questmaster = $user->getRoom()->getActorByInput($lookup);
		} else {
			foreach($user->getRoom()->getActors() as $actor) {
				if($actor instanceof Questmaster) {
					$questmaster = $actor;
					break;
				}
			}
		}
		return $questmaster;
	}
	
	/**
	private function doAccept(QuestInstance $instance, User $user, $value, $args)
	{
		$say = Alias::lookup('say');
		$questmaster = $instance->getUser();
		if(!$instance->getQuest()->isQualifiedToAccept($user, $quest))
			return $say->perform($questmaster, array($user, $questmaster->getNotQualifiedMessage()));
		
		if($instance)
		{
			$user->getQuestLog()->add(new QuestInstance($user, $instance->getQuest()));
			return $say->perform($questmaster, array($user, $questmaster->getAcceptMessage($instance)));
		}
	}

	private function doGive(QuestInstance $instance, User $user, $null, $args)
	{
		$target = $user->getRoom()->getUserByInput($args[3]);
		if(!$target)
			return Server::out($user, "They aren't here.");
		$quest = $instance->getQuest();
		$user->getQuestLog()->remove($quest);
		$target->getQuestLog()->add($quest);
		Server::out($user, "You give the quest called ".$quest->getShort()." to ".$target->getAlias().".");
	}
	*/
}
?>

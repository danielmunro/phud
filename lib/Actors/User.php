<?php
namespace Phud\Actors;
use Phud\Dbr,
	Phud\Client,
	Phud\Room\Room,
	Phud\Room\Dungeon\Dungeon,
	Phud\Races\Race,
	Phud\Abilities\Ability,
	Phud\Abilities\Skill,
	Phud\Quests\Quest,
	Phud\Quests\Log,
	Phud\Commands\Command;

class User extends Actor
{
	use Log;

	protected $hunger = 0;
	protected $thirst = 0;
	protected $full = 0;
	protected $trains = 3;
	protected $practices = 5;
	protected $password = '';
	protected $client = null;
	protected $date_created = null;
	protected $is_dm = false;
	protected $delay = 0;
	protected $quests_completed = [];
	
	public function __construct($properties = [])
	{
		$this->date_created = date('Y-m-d H:i:s');
		parent::__construct($properties);
	}

	public function applyListeners()
	{
		parent::applyListeners();
		$this->on('died', function($event, $user) {
			$user->setAttribute('hp', 1);
			$user->respawn();
			Command::lookup('look')->perform($user);
		});
	}

	public function getClient()
	{
		return $this->client;
	}
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}
	
	public function incrementDelay($delay)
	{
		$this->delay += $delay;
	}

	public function decrementDelay()
	{
		if($this->delay > 0) {
			$this->delay--;
		} 
	}

	public function getDelay()
	{
		return $this->delay;
	}

	public function getDateCreated()
	{
		return $this->date_created;
	}
	
	public function prompt()
	{
		return 'hp:' . $this->getAttribute('hp') . '/' . $this->getMaxAttribute('hp') . ' mana: ' . $this->getAttribute('mana') . '/' . $this->getMaxAttribute('mana') . ' mv: ' . $this->getAttribute('movement') . '/' . $this->getMaxAttribute('movement') . ' >';
	}
	
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function isDM()
	{
		return $this->is_dm;
	}
	
	public function setDM($is_dm)
	{
		$this->is_dm = $is_dm;
	}

	public function setRace(Race $race)
	{
		parent::setRace($race);
		$r = $this->race;
		$this->hunger = $r->getHunger();
		$this->thirst = $r->getThirst();
		$this->full = $r->getFull();
	}
	
	public function tick($event)
	{
		if(!is_resource($this->client->getSocket())) {
			$event->kill();
		}
		parent::tick();
		$this->hunger > 0 ? $this->hunger-- : null;
		$this->thirst > 0 ? $this->thirst-- : null;
		$this->full -= 2;
		if($this->full < 0) {
			$this->full = 0;
		}
		$msg = '';
		if($this->hunger === 0) {
			$msg = "You are hungry.\r\n";
		}
		if($this->thirst === 0) {
			$msg = "You are thirsty.\r\n";
		}
		$this->save();
		$this->getClient()->write($msg."\r\n".$this->prompt());
	}
	
	// Food and nourishment
	
	public function getHunger()
	{
		return $this->hunger;
	}
	
	public function getThirst()
	{
		return $this->thirst;
	}
	
	public function increaseHunger($hunger)
	{
		if($this->full + 1 > $this->race->getFull()) {
			return $this->getClient()->writeLine("You are too full.");
		}
		$this->full++;
		$this->hunger += $hunger;
		$max = $this->race->getHunger();
		if($this->hunger > $max) {
			$this->hunger = $max;
		}
		return true;
	}
	
	public function increaseThirst($thirst)
	{
		if($this->full + 1 > $this->race->getFull() || $this->thirst > $this->race->getThirst()) {
			return $this->getClient()->writeLine("You are too full.");
		}
		if($this->thirst < 0) {
			$this->thirst = 0;
		}
		if($this->full < 0) {
			$this->full = 0;
		}
		$this->full++;
		$this->thirst += $thirst;
		return true;
	}
	
	public function addTrains($trains)
	{
		$this->trains += $trains;
	}
	
	public function decreaseTrains()
	{
		$this->trains--;
	}
	
	public function getTrains()
	{
		return $this->trains;
	}
	
	public function addPractices($amount)
	{
		$this->practices += $amount;
	}
	
	public function decreasePractices()
	{
		$this->practices--;
	}
	
	public function getPractices()
	{
		return $this->practices;
	}

	public static function validateAlias($alias)
	{
		return preg_match('/^[A-Za-z]{2,12}$/i', $alias);
	}

	public function save()
	{
		$dbr = Dbr::instance();
		$dbr->set($this->alias, serialize($this));
	}

	public function finishQuest(Quest $quest)
	{
		$i = get_class($quest);
		if(isset($this->quests[$i])) {
			$q = $this->quests[$i];
			$q->reward($this);
			$this->quests_completed[$i] = $q;
			unset($this->quests[$i]);
		}
	}

	public function hasCompletedQuest(Quest $quest)
	{
		return isset($this->quests_completed[$quest->getID()]);
	}

	public function __sleep()
	{
		return [
			'hunger',
			'thirst',
			'full',
			'trains',
			'practices',
			'password',
			'date_created',
			'is_dm',
			'experience',
			'experience_per_level',
			'alias',
			'long',
			'short',
			'level',
			'gold',
			'silver',
			'copper',
			'sex',
			'disposition',
			'race',
			'room',
			'equipped',
			'alignment',
			'attributes',
			'max_attributes',
			'abilities',
			'proficiencies',
			'items',
			'affects',
			'quests',
			'quests_completed'
		];
	}

	public function __wakeup()
	{
		$this->room = Room::getByID($this->room instanceof Dungeon ? Room::getStartRoom() : $this->room->getID());
		$this->race = Race::lookup($this->race->getAlias());
		$this->race_listeners = $this->race->getListeners();
		foreach($this->race_listeners as $listener) {
			$this->on($listener[0], $listener[1]);
		}
		foreach($this->affects as $affect) {
			$affect->applyTimeoutListener($this);
		}
		foreach($this->abilities as $user_ab) {
			$ability = Ability::lookup($user_ab);
			if($ability instanceof Skill) {
				$listener = $ability->getListener();
				$this->on($listener[0], $listener[1], 'end');
			}
		}
		foreach($this->quests as $quest) {
			foreach($quest->getListeners() as $listener) {
				$this->on($listener[0], $listener[1]);
			}
		}
		$this->applyListeners();
	}
}
?>

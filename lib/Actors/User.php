<?php
namespace Phud\Actors;
use Phud\Server,
	Phud\Dbr,
	Phud\Client,
	Phud\Room,
	Phud\Races\Race,
	Phud\Abilities\Ability,
	Phud\Abilities\Skill,
	Phud\Quests\Quest,
	Phud\Quests\Log;

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
	
	public function getClient()
	{
		return $this->client;
	}
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}
	
	public function incrementDelay($delay) {
		$this->delay += $delay;
		if(empty($this->_subscriber_delay)) {
			$this->_subscriber_delay = new Subscriber(
				Event::EVENT_PULSE,
				$this,
				function($subscriber, $server, $fighter) {
					if(!$fighter->decrementDelay()) {
						$subscriber->kill();
					}
				}
			);
			Server::instance()->addSubscriber($this->_subscriber_delay);
		}

	}

	public function decrementDelay()
	{
		if($this->delay > 0) {
			$this->delay--;
			return true;
		} 
		unset($this->_subscriber_delay);
		return false;
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

	public function setRace($race)
	{
		parent::setRace($race);
		$r = $this->race['lookup'];
		$this->hunger = $r->getHunger();
		$this->thirst = $r->getThirst();
		$this->full = $r->getFull();
	}
	
	public function tick($init = false)
	{
		parent::tick();
		if(!$init)
		{
			$this->hunger > 0 ? $this->hunger-- : null;
			$this->thirst > 0 ? $this->thirst-- : null;
			$this->full -= 2;
			if($this->full < 0) {
				$this->full = 0;
			}
			if($this->hunger === 0) {
				Server::out($this, "You are hungry.");
			}
			if($this->thirst === 0) {
				Server::out($this, "You are thirsty.");
			}
			$this->save();
		}
		Server::out($this, "\n" . $this->prompt(), false);
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
		if($this->full + 1 > $this->getRace()['lookup']->getFull()) {
			return Server::out($this, "You are too full.");
		}
		$this->full++;
		$this->hunger += $hunger;
		$max = $this->getRace()['lookup']->getHunger();
		if($this->hunger > $max) {
			$this->hunger = $max;
		}
		return true;
	}
	
	public function increaseThirst($thirst)
	{
		if($this->full + 1 > $this->getRace()['lookup']->getFull() || $this->thirst > $this->getRace()['lookup']->getThirst()) {
			return Server::out($this, "You are too full.");
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
	
	public function handleDeath()
	{
		parent::handleDeath();
		$this->setAttribute('hp', 1);
		$command = Command::lookup('look');
		$command['lookup']->perform($this);
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
			'delay',
			'proficiencies',
			'items',
			'affects',
			'quests',
			'quests_completed'
		];
	}

	public function __wakeup()
	{
		$this->room = Room::find($this->room->getId());
		$this->race = Race::lookup($this->race['alias']);
		$this->race_listeners = $this->race['lookup']->getListeners();
		foreach($this->race_listeners as $listener) {
			$this->on($listener[0], $listener[1]);
		}
		foreach($this->affects as $affect) {
			$affect->applyTimeoutListener($this);
		}
		foreach($this->abilities as $user_ab) {
			$ability = Ability::lookup($user_ab);
			if($ability['lookup'] instanceof Skill) {
				$listener = $ability['lookup']->getListener();
				$this->on($listener[0], $listener[1], 'end');
			}
		}
		foreach($this->quests as $quest) {
			foreach($quest->getListeners() as $listener) {
				$this->on($listener[0], $listener[1]);
			}
		}
		Server::instance()->on('tick', $this->getTickListener());
	}
}
?>

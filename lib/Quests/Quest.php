<?php
namespace Phud\Quests;
use Phud\Actors\User,
	Phud\Debug,
	Phud\Interactive,
	Phud\Identity,
	\Exception,
	\ReflectionClass;

abstract class Quest
{
	use Identity, Interactive;

	protected $requirements_to_accept = null;
	protected $reward = null;
	protected $status = 'initialized';

	const STATUS_INITIALIZED = 'initialized';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CLOSED = 'closed';
	
	public function __construct()
	{
		if(!isset($this->id)) {
			throw new Exception('Quest error: '.$this.' needs an identifier');
		}
		self::$identities[$this->id] = $this;
	}

	public static function runInstantiation($path = '')
	{
		global $global_path;
		$d = dir($global_path.'/deploy/init/Quests/'.$path);
		while($quest = $d->read()) {
			if(substr($quest, -4) === ".php") {
				Debug::log("init quest: ".$quest);
				$class = substr($quest, 0, strpos($quest, '.'));
				$called_class = 'Phud\\Quests\\'.$class;
				$reflection = new ReflectionClass($called_class);
				if(!$reflection->isAbstract()) {
					new $called_class();
				}
			} else if(strpos($quest, '.') === false) { // directory
				self::runInstantiation($path.'/'.$quest);
			}
		}
	}

	abstract public function getListeners();

	abstract public function reward(User $user);

	abstract public function canAccept(User $user);

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function __toString()
	{
		return $this->short;
	}
}
?>

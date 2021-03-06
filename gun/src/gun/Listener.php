<?php

namespace gun;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener as MainListener;

use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerChatEvent;

use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\CommandEvent;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

use gun\npc\EventNPCTouchEvent;

class Listener implements MainListener {

	private static $listener = null;
	
	public $plugin;
	
	public static function getListener(){
		return self::$listener;
	}
	
	public function __construct(PluginBase $plugin) {
		self::$listener = $this;
		$this->plugin = $plugin;
		$this->server = $plugin->getServer();
		$this->schedule = $plugin->getScheduler();
		
		$this->registerEvents();
	}
	
	public function registerEvents(){
		$this->playerjoin = new events\PlayerJoinEvent($this);
		$this->playerquit = new events\PlayerQuitEvent($this);
		$this->receive = new events\DataPacketReceiveEvent($this);
		$this->playerlogin = new events\PlayerLoginEvent($this);
		$this->playerinteract = new events\PlayerInteractEvent($this);
		$this->itemheld = new events\PlayerItemHeldEvent($this);
		$this->playerdeath = new events\PlayerDeathEvent($this);
		$this->entityshoot = new events\EntityShootBowEvent($this);
		$this->entitydamage = new events\EntityDamageEvent($this);
		$this->playerdropitem = new events\PlayerDropItemEvent($this);
		$this->playerchat = new events\PlayerChatEvent($this);
		$this->playerrespawn = new events\PlayerRespawnEvent($this);
		$this->command = new events\CommandEvent($this);
		$this->eventnpc = new events\EventNPCTouchEvent($this);
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$this->playerjoin->call($event);
	}

	public function onAuit(PlayerQuitEvent $event){
		$this->playerquit->call($event);
	}
	
	public function onReceive(DataPacketReceiveEvent $event){
		$this->receive->call($event);
	}
	
	public function onLogin(PlayerLoginEvent $event){
		$this->playerlogin->call($event);
	}
	
	public function onInteract(PlayerInteractEvent $event){
		$this->playerinteract->call($event);
	}
	
	public function onChange(PlayerItemHeldEvent $event){
		$this->itemheld->call($event);
	}
	
	public function onDeath(PlayerDeathEvent $event){
		$this->playerdeath->call($event);
	}
	
	public function onShoot(EntityShootBowEvent $event){
		$this->entityshoot->call($event);
	}
	
	public function onDamage(EntityDamageEvent $event){
		$this->entitydamage->call($event);
	}

	public function onDropItem(PlayerDropItemEvent $event){
		$this->playerdropitem->call($event);	
	}

	public function onChat(PlayerChatEvent $event)
	{
		$this->playerchat->call($event);
	}
	
	public function onRespawn(PlayerRespawnEvent $event)
	{
		$this->playerrespawn->call($event);
	}

	public function onCommand(CommandEvent $event)
	{
		$this->command->call($event);
	}


	public function onEventNPCTouch(EventNPCTouchEvent $event)
	{
		$this->eventnpc->call($event);
	}

}

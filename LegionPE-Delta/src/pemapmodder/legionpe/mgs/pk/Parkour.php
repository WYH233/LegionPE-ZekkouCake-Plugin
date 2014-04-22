<?php

namespace pemapmodder\legionpe\mgs\pk;

use pemapmodder\legionpe\hub\HubPlugin;

use pemapmodder\utils\CallbackEventExe;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\SignPost;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;

class Parkour implements Listener{
	public function __construct(){
		$this->server = Server::getInstance();
		$pm = $this->server->getPluginManager();
		$pm->registerEvent("pocketmine\\event\\entity\\EntityMoveEvent", $this, EventPriority::HIGH, new CallbackEventExe(array($this, "onMove")), HubPlugin::get());
		$pm->registerEvent("pocketmine\\event\\player\\PlayerInteractEvent", $this, EventPriority::HIGH, new CallbackEventExe(array($this, "onInteract")), HubPlugin::get());
	}
	public function onMove(Event $event){
		if(($p = $event->getEntity()) instanceof Player){
			if($p->level->getName() === "world_parkour"){
				if($p->y <= RawLocs::fallY())
					$p->teleport(RawLocs::pk()->getSafeSpawn());
			}
		}
	}
	public function onInteract(Event $event){
		if($event->getBlock() instanceof SignPost){
			if(($pfx = RawLocs::signPrefix($event->getBlock())) !== false){
				$config = HubPlugin::get()->getDb($event->getPlayer());
				$prefixes = $config->get("prefixes");
				$prefixes["parkour"] = $pfx;
				$config->set("prefixes", $prefixes);
				$config->save();
			}
		}
	}
	public static $i = false;
	public static function get(){
		return self::$i;
	}
	public static function init(){
		self::$i = new self();
	}
}
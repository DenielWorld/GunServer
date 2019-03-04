<?php

namespace gun\events;

use pocketmine\item\Item;
use pocketmine\Server;

use gun\gameManager;
use gun\scoreboard\scoreboard;
use gun\bossbar\BossBar;

use gun\weapons\WeaponManager;

class PlayerJoinEvent extends Events {
  
  	public function __construct($api){
		parent::__construct($api);
	}

	public function call($event){
		$player = $event->getPlayer();
		$name = $player->getName();

    	$this->plugin->playerManager->setDefaultSpawn($player);
        
		$player->sendMessage('§bInfo>>§fBattleFront2に参加していただきありがとうございます');
		$player->sendMessage('§bInfo>>§fタップして操作している方は分割コントロールを推奨します');
		$player->sendMessage('§bInfo>>§fルールの確認をお願い致します');

		$event->setJoinMessage(null);
		Server::getInstance()->broadcastPopup('§b§l'.$event->getPlayer()->getName().'さんがログインしました');

		$this->plugin->playerManager->setLobbyInventory($player);
		$this->plugin->playerManager->setDefaultNameTags($player);

		$this->plugin->discordManager->sendMessage('**⭕' . $player->getName() . 'がログインしました** ' . '(' . count($this->plugin->getServer()->getOnlinePlayers()) . '/' . $this->plugin->getServer()->getMaxPlayers() . ')');
	}
}

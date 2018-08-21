<?php

declare(strict_types=1);

namespace BeatsCore\tasks;

use BeatsCore\Core;
use onebone\economyapi\EconomyAPI;
use pocketmine\scheduler\PluginTask;

class HUDTask extends PluginTask{

    /** @var Core */
    private $plugin;

    public function __construct(Core $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) : void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if(isset($this->plugin->hud[$player->getName()])){
				$faction = $this->plugin->getServer()->getPluginManager()->getPlugin("FactionsPro")->getPlayerFaction($player->getName());
				$money = EconomyAPI::getInstance()->myMoney($player);
				$x = round($player->getX());
				$y = round($player->getY());
				$z = round($player->getZ());
				$msg = "§l§dBeats§bPE §aFactions§r \n§3Faction: §b$faction §9Money: §5$money \n§eX: §6$x §l§8/ §r§eY: §6$y §l§8/ §r§eZ: §6$z";
				$player->sendPopup($msg);
			}
		}
    }
}

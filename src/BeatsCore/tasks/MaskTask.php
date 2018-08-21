<?php

declare(strict_types=1);

namespace BeatsCore\tasks;

use BeatsCore\Core;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class MaskTask extends PluginTask{

    /** @var Core */
    private $plugin;

    public function __construct(Core $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) : void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->getArmorInventory()->getHelmet()->getId() == Item::SKULL){
				switch($player->getArmorInventory()->getHelmet()->getDamage()){
					case 3: # STEVE
						$this->addEffect($player, Effect::HASTE);
						$this->addEffect($player, Effect::SPEED);
						break;
					case 4: # CREEPER
						$this->addEffect($player, Effect::NIGHT_VISION);
						$this->addEffect($player, Effect::REGENERATION);
						$this->addEffect($player, Effect::HEALTH_BOOST);
						break;
					case 5: # DRAGON
						$this->addEffect($player, Effect::HEALTH_BOOST);
						$this->addEffect($player, Effect::REGENERATION);
						$this->addEffect($player, Effect::SPEED, 4);
						break;
					case 6: # STEVE (rabbit)
						$this->addEffect($player, Effect::JUMP_BOOST, 3);
						$this->addEffect($player, Effect::SPEED, 3);
						break;
					case 7: # STEVE (witch)
						$this->addEffect($player, Effect::NIGHT_VISION);
						$this->addEffect($player, Effect::REGENERATION);
						$this->addEffect($player, Effect::HEALTH_BOOST);
						$this->addEffect($player, Effect::SPEED, 2);
						break;
					case 8: # STEVE (enderman)
						$this->addEffect($player, Effect::NIGHT_VISION);
						$this->addEffect($player, Effect::HEALTH_BOOST);
						$this->addEffect($player, Effect::SPEED, 2);
						break;
					case 9: # STEVE (chef)
						$this->addEffect($player, Effect::SATURATION);
						break;
				}
			}
		}
    }

    private function addEffect(Player $p, int $id, int $amp = 1){ // Lazy xD
    	$p->addEffect(new EffectInstance(Effect::getEffect($id), 60, $amp));
	}
}

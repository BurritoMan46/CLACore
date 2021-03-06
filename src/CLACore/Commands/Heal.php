<?php

/*
 * CLACore, a public core with many features for PocketMine-MP
 * Copyright (C) 2017-2018 CLADevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY;  without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace CLACore\Commands;

use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityRegainHealthEvent;

use CLACore\Loader;

class Heal extends BaseCommand{

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin, "heal", "Heal someone else or yourself", "/heal <player>", ["heal"]);
		$this->setPermission("clacore.command.heal");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$sender->hasPermission("clacore.command.heal")){
			$nopermission = $this->getPlugin()->langcfg->get("no.permission");
			$sender->sendMessage("$nopermission");
			return true;
		}

		if((!isset($args[0]) && !$sender instanceof Player) || count($args) > 1){
			$sender->sendMessage("Usage: /heal <player>");
			return true;
		}

		$player = $sender;
		if(isset($args[0]) && !($player = $this->getPlugin()->getServer()->getPlayer($args[0]))){
			$playernotfound = $this->getPlugin()->langcfg->get("player.not.found");
			$sender->sendMessage("$playernotfound");
			return true;
		}

		if($player->getName() !== $sender->getName() && !$sender->hasPermission("clacore.command.heal.other")){
			$nopermission = $this->getPlugin()->langcfg->get("no.permission");
			$sender->sendMessage("$nopermission");
			return true;
		}

		#player healed
		$playerhealed = $this->getPlugin()->langcfg->get("player.healed");

		#sender healed
		$senderhealed = $this->getPlugin()->langcfg->get("sender.healed");
		$senderhealed = str_replace("{player}", $player->getName(), $senderhealed);

		$player->heal(new EntityRegainHealthEvent($player, $player->getMaxHealth() - $player->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
		$player->sendMessage("$playerhealed");
		if($player !== $sender){
			$sender->sendMessage("$senderhealed");
		}
		return true;
	}
}
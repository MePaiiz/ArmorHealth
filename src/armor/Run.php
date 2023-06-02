<?php

namespace armor;

use pocketmine\scheduler\Task;

use armor\Main;

class Run extends Task{
	
	public function __construct(Main $main){
		$this->main = $main;
		}
	public function onRun($currentTick){
		$this->main->updateHP();
		}
	}
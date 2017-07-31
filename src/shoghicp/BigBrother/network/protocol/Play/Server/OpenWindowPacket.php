<?php
/**
 *  ______  __         ______               __    __
 * |   __ \|__|.-----.|   __ \.----..-----.|  |_ |  |--..-----..----.
 * |   __ <|  ||  _  ||   __ <|   _||  _  ||   _||     ||  -__||   _|
 * |______/|__||___  ||______/|__|  |_____||____||__|__||_____||__|
 *             |_____|
 *
 * BigBrother plugin for PocketMine-MP
 * Copyright (C) 2014-2015 shoghicp <https://github.com/shoghicp/BigBrother>
 * Copyright (C) 2016- BigBrotherTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author BigBrotherTeam
 * @link   https://github.com/BigBrotherTeam/BigBrother
 *
 */

namespace shoghicp\BigBrother\network\protocol\Play\Server;

use shoghicp\BigBrother\network\OutboundPacket;

class OpenWindowPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var string */
	public $inventoryType;
	/** @var string */
	public $windowTitle;
	/** @var int */
	public $slots;
	/** @var int */
	public $entityId = -1;

	public function pid(){
		return self::OPEN_WINDOW_PACKET;
	}

	public function encode(){
		$this->putByte($this->windowID);
		$this->putString($this->inventoryType);
		$this->putString($this->windowTitle);
		$this->putByte($this->slots);
		if($this->entityId !== -1){
			$this->putInt($this->entityId);
		}
	}
}
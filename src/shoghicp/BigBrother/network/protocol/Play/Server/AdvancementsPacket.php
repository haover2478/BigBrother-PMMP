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

class AdvancementsPacket extends OutboundPacket{

	/** @var boolean */
	public $doClear = false;
	/** @var array */
	public $advancements = [];
	/** @var array */
	public $identifiers = [];
	/** @var array */
	public $progress = [];

	public function pid(){
		return self::ADVANCEMENTS_PACKET;
	}

	public function encode(){
		$this->putByte($this->doClear > 0);
		$this->putVarInt(count($this->advancements));
		foreach($this->advancements as $advancement){
			$this->putString($advancement[0]);//id
			$this->putByte($advancement[1][0] > 0);//has parent
			if($advancement[1][0]){
				$this->putString($advancement[1][1]);//parent id
			}
			$this->putByte($advancement[2][0] > 0);//has display
			if($advancement[2][0]){
				echo "aaaa\n";
				$this->putString($advancement[2][1]);//title
				$this->putString($advancement[2][2]);//description
				$this->putSlot($advancement[2][3]);//icon (item)
				$this->putVarInt($advancement[2][4]);// frame type
				$this->putInt($advancement[2][5][0]);// flag
				if(($advancement[2][5][0] & 0x01) > 0){
					$this->putString($advancement[2][5][1]);
				}
				$this->putFloat($advancement[2][6]);// x coord
				$this->putFloat($advancement[2][7]);// z coord
			}
			$this->putVarInt(count($advancement[3]));//criteria
			foreach($advancement[3] as $criteria){
				$this->putString($criteria[0]);//key
				//value but void
			}
			$this->putVarInt(count($advancement[4]));
			foreach($advancement[4] as $requirements){//Requirements
				$this->putVarInt(count($requirements));
				foreach($requirements as $requirement){
					$this->putString($requirement);
				}
			}
		}
		$this->putVarInt(count($this->identifiers));
		/*foreach($this->identifiers as $identifier){
			$this->putString($identifier);
		}*/
		$this->putVarInt(count($this->progress));
		/*foreach($this->progress as $progressdata){
			$this->putString($progressdata[0]);//id
			$this->putVarInt(count($progressdata[1]));//Criteria size
			foreach($progressdata[1] as $criterion){
				$this->putString($criterion[0]);
				$this->putByte($criterion[1][0] > 0);
				if($criterion[1][0]){
					$this->putLong($criterion[1][1]);//time
				}
			}
		}*/
		var_dump($this);
	}
}
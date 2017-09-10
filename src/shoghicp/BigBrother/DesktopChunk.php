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

namespace shoghicp\BigBrother;

use pocketmine\level\Level;
use pocketmine\level\format\io\LevelProvider;
use shoghicp\BigBrother\utils\Binary;
use shoghicp\BigBrother\utils\ConvertUtils;


class DesktopChunk{
	/** @var DesktopPlayer */
	private $player;
	/** @var int */
	private $chunkX;
	/** @var int */
	private $chunkZ;
	/** @var LevelProvider */
	private  $provider;
	/** @var bool */
	private $groundup;
	/** @var int */
	private $bitmap;
	/** @var string */
	private $biomes;
	/** @var string */
	private $data;

	public function __construct(DesktopPlayer $player, int $chunkX, int $chunkZ){
		$this->player = $player;
		$this->chunkX = $chunkX;
		$this->chunkZ = $chunkZ;
		$this->provider = $player->getLevel()->getProvider();
		$this->groundup = true;
		$this->bitmap = 0;
		$this->data = $this->generateChunk();
	}

	public function generateChunk() : string{
		$chunk = $this->provider->getChunk($this->chunkX, $this->chunkZ, false);
		$this->biomes = $chunk->getBiomeIdArray();

		$payload = "";
		foreach($chunk->getSubChunks() as $num => $subChunk){
			if($subChunk->isEmpty()){
				continue;
			}

			$this->bitmap |= 0x01 << $num;

			$palette = [];
			$bitsperblock = 8;

			$chunkdata = "";
			$blocklightdata = "";
			$skylightdata = "";

			for($y = 0; $y < 16; ++$y){
				for($z = 0; $z < 16; ++$z){

					$data = "";
					for($x = 0; $x < 16; ++$x){
						$blockid = $subChunk->getBlockId($x, $y, $z);
						$blockdata = $subChunk->getBlockData($x, $y, $z);

						ConvertUtils::convertBlockData(true, $blockid, $blockdata);
						$block = (int) ($blockid << 4) | $blockdata;

						if(($key = array_search($block, $palette, true)) !== false){
							$data .= chr($key);//bit
						}else{
							$key = count($palette);
							$palette[$key] = $block;

							$data .= chr($key);//bit
						}

						if($x === 7 or $x === 15){//Reset ChunkData
							$chunkdata .= strrev($data);
							$blocklightdata .= str_repeat("\xff", 4);
							$skylightdata .= str_repeat("\xff", 4);
							$blocklight = "";
							$skylight = "";
							$data = "";
						}
					}
				}
			}

			/* Bits Per Block & Palette Length */
			$payload .= Binary::writeByte($bitsperblock).Binary::writeComputerVarInt(count($palette));

			/* Palette */
			foreach($palette as $num => $value){
				$payload .= Binary::writeComputerVarInt($value);
			}

			/* Data Array Length */
			$payload .= Binary::writeComputerVarInt(strlen($chunkdata) / 8);

			/* Data Array */
			$payload .= $chunkdata;

			/* Block Light*/
			$payload .= $blocklightdata;

			/* Sky Light Only overworld */
			if($this->player->bigBrother_getDimension() === 0){
				$payload .= $skylightdata;
			}
		}

		return $payload;
	}

	public function isGroundUp() : bool{
		return $this->groundup;
	}

	public function getBitMapData() : int{
		return $this->bitmap;
	}

	public function getBiomesData() : string{
		return $this->biomes;
	}

	public function getChunkData() : string{
		return $this->data;
	}
}

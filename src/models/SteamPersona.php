<?php

/**
 *     Written by Daniel Faria Sampaio <dan.faria.sampaio@gmail.com>
 *     Copyright 2014 Daniel Faria Sampaio
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	Representation of a Steam persona. 
 *  Most of the calculations made in this class regarding the 'SteamId' and all of his representations are available on < https://developer.valvesoftware.com/wiki/SteamID >. *  
 *  The SteamId validation algorithms (isSteamId and isSteam64) in this class was made by Nico Bergemann < barracuda415@yahoo.de >.
 *  
 * @author Daniel Faria Sampaio
 */

require_once('D:/xampp/htdocs/steamify/src/utils/SteamEnums.php');

class SteamPersona {
	const STEAM_BASE_ID = '76561197960265728';
	
	// TODO: Put your API_KEY here. If you don't have one yet, get one at: < http://www.steamcommunity.com/dev/apikey >	
	const API_KEY = 'API_KEY GOES HERE';
	
	private $steamId;		// 32-bit representation of Steam Id.       Format: STEAM_{Universe}:{Account-Type}:{Account-Number}
	private $steamId3;		// *NEW* representation created by Valve.   Format: [U:1:{Account-Number * 2}]
	private $steamId64;		// 64-bit representation of Steam Id. 
	
	private $realName;		// User's full name
	private $personaName;	// Profile's nickname
	private $profileUrl;	// Profile's Community URL
	private $timeCreated;
	
	private $personaState;	// Profile's current state. See utils/SteamEnums::$PERSONA_STATE array for more information
	private $lastLogoff;	// The Timestamp of last Logoff
	
	private $gameId;
	private $gameName;
	private $gameServerIp;	
	
	private $smallAvatar;
	private $mediumAvatar;
	private $fullAvatar;
	
	
	public function __construct($steamId) {
		$this->calculateIds($steamId);
		$this->fetchSteamData();
	}
	
	/**
	 * Used to calculate the remaining kinds of Ids from a given Id.
	 */
	protected function calculateIds($steamId) {
		if (self::isSteamId($steamId)) {
			$this->steamId = $steamId;
			$this->steamId64 = $this->toSteamId64($steamId);
			$this->steamId3 = $this->toSteamId3($steamId);
		} else {
			if (self::isSteamId64($steamId)) {
				$this->steamId64 = $steamId;
				$this->steamId = $this->toSteamId($steamId);
				$this->steamId3 = $this->toSteamId3($steamId);
			} else {
				if (self::isSteamId3($steamId)) {
					$this->steamId3 = $steamId;
					$this->steamId = $this->toSteamId($steamId);
					$this->steamId64 = $this->toSteamId64($steamId);
				}
			}
		}
	}
	
	/**
	 * Used to fetch public data from the SteamId's Steam profile.
	 * Make sure you have cURL supported in your system.
	 */
	// TODO: colocar em classe Controller
	private final function fetchSteamData() {
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . self::API_KEY . "&steamids=" . $this->getSteamId64(); 
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				
		$output = curl_exec($ch);
		
		if (!curl_errno($ch)) {
			$steamProfile = json_decode($output, true)['response']['players'][0];
			$this->setSteamProfileAttributes($steamProfile);
		}
	}
	
	private final function setSteamProfileAttributes($steamProfile) {
		if (array_key_exists('personaname', $steamProfile)) $this->personaName = $steamProfile['personaname'];
		if (array_key_exists('timecreated', $steamProfile)) $this->timeCreated = $steamProfile['timecreated'];
		if (array_key_exists('lastlogoff', $steamProfile)) $this->lastLogoff = $steamProfile['lastlogoff'];
		if (array_key_exists('profileurl', $steamProfile)) $this->profileUrl = $steamProfile['profileurl'];
		if (array_key_exists('avatar', $steamProfile)) $this->smallAvatar = $steamProfile['avatar'];
		if (array_key_exists('avatarmedium', $steamProfile)) $this->mediumAvatar = $steamProfile['avatarmedium'];
		if (array_key_exists('avatarfull', $steamProfile)) $this->fullAvatar = $steamProfile['avatarfull'];
		if (array_key_exists('realname', $steamProfile)) $this->realName = $steamProfile['realname'];
		if (array_key_exists('personastate', $steamProfile)) $this->personaState = $steamProfile['personastate'];
		if (array_key_exists('gameid', $steamProfile)) $this->gameId = $steamProfile['gameid'];
		if (array_key_exists('gameextrainfo', $steamProfile)) $this->gameName = $steamProfile['gameextrainfo'];
		if (array_key_exists('gameserverip', $steamProfile)) $this->gameServerIp = $steamProfile['gameserverip'];
	}
	
	/**
	 *  Used to obatin the SteamID using either the SteamID3 or the SteamID64.
	 * 'SteamId' or 'SteamId32' is composed by three fields:
	 *  STEAM_[Universe]:[Account-Type]:[Account-Number]
	 *  For more information visit: <a href="https://developer.valvesoftware.com/wiki/SteamID#Format">Click here</a>.
	 * @param string $steamId : either a 'SteamId64' or a 'SteamId3'.
	 */
	protected function toSteamId($steamId) {
		$isSteamId3 = self::isSteamId3($steamId);
		$isSteamId64 = self::isSteamId64($steamId);
		 
		if (!($isSteamId3) && !($isSteamId64)) {
			throw new InvalidArgumentException ("Argument isn't either 'SteamId64' or 'SteamId3'.");
		}		
		
		$universe = '0';
		
		if ($isSteamId64) {			
			$account_type = bcmod($steamId, '2') == '0' ? '0' : '1';			
			$account_number = bcdiv(bcsub(bcsub($steamId, $account_type), self::STEAM_BASE_ID), '2');
			
		} else {
			$trimmedSteamId3 = rtrim(explode(':', substr($steamId, 5))[0], ']');
			$account_type = bcmod(bcadd($trimmedSteamId3, self::STEAM_BASE_ID), '2') == '0' ? '0' : '1';
			$account_number = bcdiv($trimmedSteamId3, '2');
		}
		
		return "STEAM_$universe:$account_type:$account_number";
	}
	
	/**
	 * Used to obtain the SteamID3 using either the SteamID or the SteamID64.
	 * @param string $steamId
	 */
	protected function toSteamId3($steamId) {
		$isSteamId = self::isSteamId($steamId);
		$isSteamId64 = self::isSteamId64($steamId);
		
		if (!($isSteamId) && !($isSteamId64)) {
			throw new InvalidArgumentException("Argument isn't either 'SteamId' or 'SteamId64'.");
		}
		
		$account_number = bcsub($this->steamId64, self::STEAM_BASE_ID);
		return "[U:1:$account_number]";
	}
	
	/**
	 * Used to obtain the SteamID64 using either the SteamID3 or the SteamID.
	 * You can find the formula and more informations at: https://developer.valvesoftware.com/wiki/SteamID#Steam_ID_as_a_Steam_Community_ID
	 * @param string $steamId : either a 'SteamId' or a 'SteamId3'.
	 */
	protected function toSteamId64($steamId) {
		$isSteamId = self::isSteamId($steamId);
		$isSteamId3 = self::isSteamId3($steamId);
		
		if (!($isSteamId) && !($isSteamId3)) {
			throw new InvalidArgumentException("Argument isn't either SteamId or SteamId3");
		}
		
		if ($isSteamId) {
			$eSteamId = explode(':', substr($steamId, 6));
			$account_type = $eSteamId[1];
			$account_number = $eSteamId[2];
			return bcadd(bcadd(bcmul($account_number, '2'), $account_type), self::STEAM_BASE_ID);			
		} else {
			$trimmedSteamId3 = rtrim(explode(':', substr($steamId, 5))[0], ']');
			$account_type = bcmod(bcadd($trimmedSteamId3, self::STEAM_BASE_ID), '2') == '0' ? '0' : '1';
			return bcadd($trimmedSteamId3, self::STEAM_BASE_ID);
		}
	}
	
	/**
	 * Used to verify if the parameter is a valid SteamId.
	 * @param string $steamId
	 * @return boolean
	 */
	public static function isSteamId($steamId) {
		return preg_match('/^(STEAM_)?[0-5]:[0-9]:\d+$/i', $steamId);
	}
	
	/**
	 * Used to verify if the parameter is a valid SteamId3.
	 * @param string $steamId
	 * @return boolean
	 */
	public static function isSteamId3($steamId) {
		return preg_match('/^(\\[U:1:)\d+(\\])$/i', $steamId);
	}
	
	/**
	 * Used to verify if the parameter is a valid SteamId64.
	 * @param string $steamId
	 * @return boolean
	 */
	public static function isSteamId64($steamId) {
		if (!preg_match('/^\d+$/i', $steamId)) {
			return false;
		}
		if (bccomp(self::STEAM_BASE_ID, $steamId) == 1) {
			return false;
		}
		return true;
	}
	
	public function getSteamId() {
		return $this->steamId;
	}
	
	public function getSteamId3() {
		return $this->steamId3;
	}
	
	public function getSteamId64() {
		return $this->steamId64;
	}
	
	public function getFullAvatar() {
		return $this->fullAvatar;
	}
	
	public function getMediumAvatar() {
		return $this->mediumAvatar;
	}
	
	public function getSmallAvatar() {
		return $this->smallAvatar;
	}
	
	public function getPersonaName() {
		return $this->personaName;
	}
	
	public function getProfileUrl() {
		return $this->profileUrl;
	}
	
	/**
	 * Get the current state of the Steam Profile.
	 * @return string
	 */
	public function getState() {
		if ($this->gameServerIp != null && $this->gameServerIp != '0.0.0.0:0')
			return $this->personaState > 0 && $this->gameId != null ? "Playing '$this->gameName' at {$this->gameServerIp}." : SteamEnums::$PERSONA_STATE[$this->personaState];
		return $this->personaState > 0 && $this->gameId != null ? "Playing '{$this->gameName}'." : SteamEnums::$PERSONA_STATE[$this->personaState];
	}
}
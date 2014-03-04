<?php

/**
 * Ingress API
 *
 * @author chazz
 */
class API {

	private $handshakeData = array(
		'nemesisSoftwareVersion' => '2014-02-24T19:17:49Z+31337a6a12db+opt', // 1.46.1
		'deviceSoftwareVersion'  => '4.2'
	);
	private $hs_uri = 'https://m-dot-betaspike.appspot.com/handshake?json=';
	private $rpc_uri = 'https://m-dot-betaspike.appspot.com/rpc/';
	private $host = 'm-dot-betaspike.appspot.com';
	private $xsrfToken = '';
	private $sacsid = '';
	private $knobSyncTimestamp = '';
	private $playerName;
	private $playerAP;
	private $playerEnergy;
	private $playerLevel;
	private $playerTeam;
	private $playerAPDiff;
	private $levelAp = array(0, 2500, 20000, 70000, 150000, 300000, 600000, 1200000,);
	private $levelXM = array(3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000,);

	private $resonatorEnergyLevel = array(1000, 1500, 2000, 2500, 3000, 4000, 5000, 6000);
	private $inventory = array(
		'EMITTER_A'       => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0),
		'EMP_BURSTER'     => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0),
		'ULTRA_STRIKE'    => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0),
		'RES_SHIELD'      => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'FORCE_AMP'       => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'HEATSINK'        => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'LINK_AMPLIFIER'  => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'MULTIHACK'       => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'TURRET'          => array('COMMON' => 0, 'RARE' => 0, 'VERY_RARE' => 0),
		'POWER_CUBE'      => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0),
		'MEDIA'           => array(),
		'FLIP_CARD'       => array('total' => 0, 'ADA' => 0, 'JARVIS' => 0),
		'PORTAL_LINK_KEY' => array(),
	);


	private $entity;


	public function getUrl() {
		return $this->hs_uri . json_encode($this->handshakeData);
	}


	private $collect_items = false;
	private $collect_type;
	private $collect_count;
	private $collect_level;
	private $collected = array();


	const ITEM_RESONATOR = 'EMITTER_A';
	const ITEM_SHIELD    = 'RES_SHIELD';
	const ITEM_FORCE_AMP = 'FORCE_AMP';
	const ITEM_HEATSINK  = 'HEATSINK';
	const ITEM_LINK_AMP  = 'LINK_AMPLIFIER';
	const ITEM_MULTIHACK = 'MULTIHACK';
	const ITEM_TURRET    = 'TURRET';
	const ITEM_BURSTER   = 'EMP_BURSTER';
	const ITEM_CUBE      = 'POWER_CUBE';
	const ITEM_FLIP_CARD = 'FLIP_CARD';


	private $counted_items;

	public $location = array('lat' => 0, 'lng' => 0);

	private $rarytiFromLvlMap = array(1 => 'COMMON', 2 => 'RARE', '3' => 'VERY_RARE');


	private static $nicknames = array();


	private $error;


	/* main comm methods */
	public function handshake() {


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->hs_uri . json_encode($this->handshakeData));



		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json;charset=UTF-8',
			'User-Agent: Nemesis (gzip)',
			'Host: ' . $this->host,
			'Connection: Keep-Alive',
			'Cookie: SACSID=' . $this->sacsid . ';path=/',
		));

		$res = curl_exec($ch);


		curl_close($ch);


		if ($res == false)
			return false;


		list($header, $body) = explode("\r\n\r\n", $res, 2);

		$heads = (explode("\r\n", $header));


		foreach ($heads as $h) {
			if (strstr($h, 'Set-Cookie:')) {
				$chunks = (explode('Set-Cookie: ', $h));
				$sacsid = explode('; ', $chunks[1]);

				$this->sacsid = str_replace('SACSID=', '', $sacsid[0]);


			}
		}


		if ($body == '')
			return false;

		$res = str_replace('while(1);', '', $body);


		$handshake = json_decode($res, true);

		if ($handshake == null)
			return false;


		$this->xsrfToken = (@$handshake['result']['xsrfToken']);

		$this->playerName   = @$handshake['result']['nickname'];
		$this->playerTeam   = @$handshake['result']['playerEntity']['2']['controllingTeam']['team'];
		$this->playerAP     = @$handshake['result']['playerEntity'][2]['playerPersonal']['ap'];
		$this->playerEnergy = @$handshake['result']['playerEntity']['2']['playerPersonal']['energy'];


		$this->knobSyncTimestamp = (int)((int)@$handshake['result']['initialKnobs']['syncTimestamp']);

		return true;
	}

	private function sendRequest($request, $params = array()) {


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->rpc_uri . $request);
		curl_setopt($ch, CURLOPT_POST, 1);
		$json_request = json_encode(array('params' => $params));


		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json;charset=UTF-8',
			'User-Agent: Nemesis (gzip)',

			'X-XsrfToken: ' . $this->xsrfToken,
			'Host: ' . $this->host,
			'Connection: Keep-Alive',
			'Cookie: SACSID=' . $this->sacsid . ';path=/',
		));

		$resp = curl_exec($ch);


		$info = curl_getinfo($ch);

		curl_close($ch);

		if ($info['http_code'] != 200)
			return false;

		$json_resp = json_decode($resp, true);


		if ($json_resp == null) {

			return false;
		}

		$this->processGameBasket($json_resp['gameBasket']);

		return $json_resp;

	}


	/* process methods */


	private function processGameBasket($gameBasket) {
		if (isset($gameBasket['inventory']) AND count($gameBasket['inventory']) > 0)
			$this->processInventory($gameBasket['inventory']);

		if (isset($gameBasket['gameEntities']) AND count($gameBasket['gameEntities']) > 0)
			$this->processGameEntities($gameBasket['gameEntities']);
	}

	private function processGameEntities($entities) {
		$this->entity = $entities;
	}

	private function processInventory($inventory) {


		foreach ($inventory as $item) {
			if (isset($this->counted_items[$item[0]]))
				continue;

			if (isset($item[2]["resourceWithLevels"]["resourceType"])) {
				$resourceType = $item[2]["resourceWithLevels"]["resourceType"];
			}

			if (isset($item[2]["resource"]["resourceType"])) {
				$resourceType = $item[2]["resource"]["resourceType"];
			}

			if (isset($item[2]["modResource"]["resourceType"])) {
				$resourceType = $item[2]["modResource"]["resourceType"];
			}


			if ($resourceType == 'EMITTER_A' || $resourceType == 'EMP_BURSTER' || $resourceType == 'POWER_CUBE' || $resourceType == 'ULTRA_STRIKE') {
				$this->inventory[$resourceType][$item[2]['resourceWithLevels']['level']] += 1;
			} else if (in_array($resourceType, array('RES_SHIELD', 'FORCE_AMP', 'HEATSINK', 'LINK_AMPLIFIER', 'MULTIHACK', 'TURRET'))) {
				$this->inventory[$resourceType][$item[2]['modResource']['rarity']] += 1;
			} else if ($resourceType == 'FLIP_CARD') {
				$this->inventory[$resourceType]['total'] += 1;
				$this->inventory[$resourceType][$item[2]['flipCard']['flipCardType']] += 1;
			} else if ($resourceType == 'PORTAL_LINK_KEY') {
				$loc    = explode(',', $item[2]['portalCoupler']['portalLocation']);
				$loc[0] = hexdec($loc[0]) / 1e6;
				$loc[1] = hexdec($loc[1]) / 1e6;
				$guid   = $item[2]['portalCoupler']['portalGuid'];
				if (isset($this->inventory[$resourceType][$guid])) {
					$this->inventory[$resourceType][$guid]['count']++;
					$this->inventory[$resourceType][$guid]['keyGuids'][] = $item[0];
				} else {
					$this->inventory[$resourceType][$guid] = array(
						'count'     => 1,
						'image'     => $item[2]['portalCoupler']['portalImageUrl'],
						'title'     => $item[2]['portalCoupler']['portalTitle'],
						'address'   => $item[2]['portalCoupler']['portalAddress'],
						'location'  => array('lat' => $loc[0], 'long' => $loc[1]),
						'timestamp' => $item[1],
						'keyGuids'  => array($item[0])
					);
				}
			} else if ($resourceType == 'MEDIA') {
				$guid = $item[0];
				if (isset($this->inventory[$resourceType][$guid])) {
					$this->inventory[$resourceType][$guid]['count']++;
				} else {
					$this->inventory[$resourceType][$guid] = array(
						'count' => 1,
						'name'  => $item[2]["storyItem"]["shortDescription"],
						'url'   => $item[2]["storyItem"]["primaryUrl"],
						'image' => $item[2]["imageByUrl"]["imageUrl"],
						'level' => $item[2]["resourceWithLevels"]["level"],
					);
				}
			}

			$this->counted_items[$item[0]] = 1;


			if ($this->collect_items AND count($this->collected) < $this->collect_count) {
				if ($resourceType == $this->collect_type)
					if ((in_array($this->collect_type, array(self::ITEM_RESONATOR, self::ITEM_BURSTER)) AND (int)$item[2]['resourceWithLevels']['level'] == $this->collect_level) OR
						in_array($this->collect_type, array(self::ITEM_FORCE_AMP, self::ITEM_HEATSINK, self::ITEM_LINK_AMP, self::ITEM_MULTIHACK, self::ITEM_SHIELD, self::ITEM_TURRET)) AND $item[2]['modResource']['rarity'] == $this->rarytiFromLvlMap[$this->collect_level]
					)
						$this->collected[] = $item[0];
			}
		}


		foreach ($this->inventory as &$value) {
			ksort($value);
		}
	}

	public function getInventory() {
		$data = $this->sendRequest('playerUndecorated/getInventory', array('lastQueryTimestamp' => 0));

		if ($data) {

			$data = $this->sendRequest('playerUndecorated/getInventory', array('lastQueryTimestamp' => (int)$data['result']));

			return $this->inventory;
		} else {
			return false;
		}
	}


	public function getInvitesNum() {
		$data = $this->sendRequest('playerUndecorated/getInviteInfo');

		if ($data) {
			return $data['result']['numAvailableInvites'];
		}

		return false;
	}

	public function getScore() {
		$data = $this->sendRequest('playerUndecorated/getGameScore');

		if ($data)
			return $data['result'];

		return false;
	}


	public function getModifiedEntities($giud = '', $timestamp = 0) {
		$params = array(

			"clientBasket"      => array(
				"clientBlob" => "",
			),
			"energyGlobGuids"   => array(),
			"guids"          => array($giud),
			"knobSyncTimestamp" => $this->knobSyncTimestamp,
			"location"    => $this->formatLocation(0, 0),
			"timestampsMs"      => array($timestamp),
		);

		$data = $this->sendRequest('gameplay/getModifiedEntitiesByGuid', $params);

		if ($data)
			return $this->entity;


		return false;

	}

	public function getNicknameFromUserGUID($guid) {

		if (isset(self::$nicknames[$guid]))
			return self::$nicknames[$guid];
		$params = array(
			array(
				$guid,
			)
		);
		$data   = $this->sendRequest('playerUndecorated/getNickNamesFromPlayerIds', $params);
		if ($data) {
			self::$nicknames[$guid] = $data['result'][0];

			return self::$nicknames[$guid];

		}

		return false;
	}


	public function getItemsGuids($item_type, $level, $count) {
		$this->collect_items = true;
		$this->collect_type  = $item_type;
		$this->collect_level = $level;
		$this->collect_count = $count;

		$data = $this->getInventory();

		if (!$data)
			return array();

		return ($this->collected);
	}

	public function recycleItem($itemGuid) {
		$params = array(

			"clientBasket"      => array(
				"clientBlob" => null,
			),
			"energyGlobGuids"   => array(),
			"itemGuid"          => $itemGuid,
			"knobSyncTimestamp" => $this->knobSyncTimestamp,
			"playerLocation"    => $this->formatLocation(0, 0),
		);

		$data = $this->sendRequest('gameplay/recycleItem', $params);
		if (!$data)
			return false;

		return true;

	}

	public function redeemReward($passcode) {

		$params = array($passcode);

		$data = $this->sendRequest('playerUndecorated/redeemReward', $params);

		if (!$data)
			return false;


		if (isset($data['error'])) {

			if ($data['error'] == 'INVALID_PASSCODE')
				$this->error = ('Invalid passcode');
			else if ($data['error'] == 'ALREADY_REDEEMED')
				$this->error = ('Already redeemed');
			else if ($data['error'] == 'ALREADY_REDEEMED_BY_PLAYER')
				$this->error = ('Aready redeemed by you!');
			else {
				$this->error = ('Uknown error!');
			}

			return false;
		} else if (isset($data['result'])) {
			return array('result' => 'Passcode accepted', 'apAward' => (isset($data['result']['apAward']) ? $data['result']['apAward'] : 0), 'xmAward' => (isset($data['result']['xmAward']) ? $data['result']['xmAward'] : 0), 'items' => $this->inventory);
		} else
			return array('error' => 'Uknown error!');


	}


	public function getPlayerProfile($playerName) {
		$params = array($playerName);

		$data = $this->sendRequest('playerUndecorated/getPlayerProfile', $params);

		if (!$data)
			return false;

		if (isset($data['error']))
			return false;


		$next = $this->getPaginatedDisplayedAchievements($playerName, $data['result']['firstAchievementContinuationToken']);

		if ($next != false) {
			foreach ($next['displayedAchievements'] as $achv) {
				$data['result']['highlightedAchievements'][] = $achv;
			}
		}

		return $data['result'];
	}

	public function getPaginatedDisplayedAchievements($playerName, $contToken) {
		$params = array(
			'playerNickname'    => $playerName,
			'continuationToken' => (int)$contToken,
		);

		$data = $this->sendRequest('playerUndecorated/getPaginatedDisplayedAchievements', array($params));

		if (!$data)
			return false;

		if (isset($data['error']))
			return false;

		return $data['result'];
	}


	public function getGlobalScore() {
		$params = array(
			'cellIdToken'     => null,
			'location'        => null,
			'scoreCycleTitle' => null,
		);


		$data = $this->sendRequest('playerUndecorated/getGlobalScore', $params);


		if (!$data)
			return false;

		if (isset($data['error'])) {
			$this->error = $data['error']['failMessage'];

			return false;
		}

		return $data['result'];
	}

	public function getRegionScoreActivity($cellId) {
		$params = array(
			'cellIdToken'     => null,
			'location'        => $this->formatLocation(0, 0),
			'scoreCycleTitle' => null,
		);


		$data = $this->sendRequest('playerUndecorated/getRegionScoreActivity', $params);


		if (!$data)
			return false;

		if (isset($data['error'])) {
			$this->error = $data['error']['failMessage'];

			return false;
		}

		return $data['result'];
	}

	public function getRegionScore($cellId, $scoreCycleTitle = null) {


		$params = array(
			'cellIdToken'     => $cellId,
			'location'        => null,
			'scoreCycleTitle' => $scoreCycleTitle,
		);


		$data = $this->sendRequest('playerUndecorated/getRegionScore', $params);


		if (!$data)
			return false;


		if (isset($data['error'])) {
			$this->error = $data['error']['failMessage'];

			return false;
		}

		return $data['result'];
	}

	public function getPaginatedRegionScoreHistory($cellId, $scoreCycleTitle = null) {


		$params = array(
			'cellIdToken'     => $cellId,
			'location'        => null,
			'scoreCycleTitle' => $scoreCycleTitle,
		);


		$data = $this->sendRequest('playerUndecorated/getPaginatedRegionScoreHistory', $params);


		if (!$data)
			return false;


		if (isset($data['error'])) {
			$this->error = $data['error']['failMessage'];

			return false;
		}

		return $data['result'];
	}

	public function getRegionScoreLeaderBoard($cellId, $scoreCycleTitle = null) {


		$params = array(
			'cellIdToken'     => $cellId,
			'location'        => null,
			'scoreCycleTitle' => $scoreCycleTitle,
		);


		$data = $this->sendRequest('playerUndecorated/getRegionScoreLeaderBoard', $params);


		if (!$data)
			return false;


		if (isset($data['error'])) {
			$this->error = $data['error']['failMessage'];

			return false;
		}

		return $data['result'];
	}


	/* helper methods */

	public function getPlayerLevelByAp($player_ap) {


		foreach ($this->levelAp as $lvl => $ap) {
			if ($player_ap < $ap) {
				return $lvl;

			}
		}

		return 8;
	}

	public function setSACSID($sacsid) {
		$this->sacsid = $sacsid;
	}

	public function getSACSID() {
		return $this->sacsid;
	}


	public function getXsrfToken() {
		return $this->xsrfToken;
	}

	public function setXsrfToken($token) {
		$this->xsrfToken = $token;
	}

	public function formatLocation($lat, $lng) {
		return sprintf("%08x,%08x", intval($lat * 1E6), intval($lng * 1E6));
	}

	public function getPlayerName() {

		return $this->playerName;
	}

	public function getPlayerTeam() {
		return $this->playerTeam;
	}

	public function getPlayerEnergy() {
		return $this->playerEnergy;
	}

	public function getPlayerAP() {
		return $this->playerAP;
	}

	public function getPlayerDiffAp() {

		if (isset($this->playerAPDiff))
			return $this->playerAPDiff;

		foreach ($this->levelAp as $ap) {
			if ($this->playerAP < $ap) {
				$this->playerAPDiff = $ap - $this->playerAP;

				return $this->playerAPDiff;
			}
		}

		return 0;
	}

	public function getPlayerLevel() {
		if (isset($this->playerLevel))
			return $this->playerLevel;

		foreach ($this->levelAp as $lvl => $ap) {
			if ($this->playerAP < $ap) {
				$this->playerLevel = $lvl;

				return $this->playerLevel;
			}
		}
		$this->playerLevel = 8;

		return $this->playerLevel;
	}


	public function resetLocation() {
		$this->location = array('lat' => 0, 'lng' => 0);
	}

	public function setLocation($lat, $lng) {
		$this->location = array('lat' => $lat, 'lng' => $lng);
	}


	public function getError() {
		return $this->error;
	}

	function is_assoc($array) {
		foreach (array_keys($array) as $k => $v) {
			if ($k !== $v)
				return true;
		}

		return false;
	}

	public function getPlayerApPercents() {

		$diff  = $this->getPlayerDiffAp();
		$level = $this->getPlayerLevel();
		if ($level == 8)
			return 0;


		$level_diff = $this->levelAp[$level] - $this->levelAp[$level - 1];
		$diff       = $level_diff - $diff;

		return ((int)(($diff / $level_diff) * 100));
	}

	public function getXmForLevel($level) {
		return $this->levelXM[$level - 1];
	}

	public function getApForLevel($level) {

		return $this->levelAp[$level - 1];
	}

	public function getResonatorEnergyLevel($level) {
		return $this->resonatorEnergyLevel[$level - 1];
	}

	public function getLevelByInt($intLvl){
		switch ((int)$intLvl) {
			case 1:case 2:case 3:case 4:case 5:
				return 1;
			case 6:case 7:case 8:case 9:case 10:
				return 2;
			case 11:case 12:case 13:case 14:case 15:
				return 3;
			case 16:case 17:case 18:case 19:case 20:
				return 4;
			case 21:case 22:case 23:case 24:case 25:
				return 5;
			case 26:case 27:case 28:case 29:case 30:
				return 6;
			case 31:case 32:case 33:case 34:case 35:
				return 7;
			case 36:
				return 8;
		}
	}

	public function getNemesisSoftwareVersion(){

		return json_encode($this->handshakeData);
	}
}

?>
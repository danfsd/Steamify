<?php
	require_once '../src/models/SteamPersona.php';
?>
<?php 	
	function validateConversion64To32() {
		$steamIds64 = ['76561198024818355', '76561198061528341', '76561198086402065', '76561197963177867'];
		$expectedIds = ['STEAM_0:1:32276313', 'STEAM_0:1:50631306', 'STEAM_0:1:63068168', 'STEAM_0:1:1456069'];
		echo "Validating conversion 64-bit to 32-bit</br>";
		for($i = 0; $i < count($steamIds64); $i++) {
			$steamPersona = new SteamPersona($steamIds64[$i]);
			echo "Converted SteamId: " . $steamPersona->getSteamId() . "</br>";
			echo "Is it correct? => " . (strcmp($steamPersona->getSteamId(), $expectedIds[$i]) == 0) . "</br>";
			assert(strcmp($steamPersona->getSteamId(), $expectedIds[$i]) == 0, "Converted SteamId is wrong. [Expected: '{$expectedIds[$i]}', Actual: '{$steamPersona->getSteamId()}'");
		}
		echo "</br>";
	}
	
	validateConversion64To32();
	
	function validateConversion32To64() {
		$steamIds = ['STEAM_0:1:32276313', 'STEAM_0:1:50631306', 'STEAM_0:1:63068168', 'STEAM_0:1:1456069'];
		$expectedIds = ['76561198024818355', '76561198061528341', '76561198086402065', '76561197963177867'];
		echo "Validating conversion 32-bit to 64-bit</br>";
		for($i = 0; $i < count($steamIds); $i++) {
			$steamPersona = new SteamPersona($steamIds[$i]);
			echo "Converted SteamId: " . $steamPersona->getSteamId64() . "</br>";
			echo "Is it correct? => " . (strcmp($steamPersona->getSteamId64(), $expectedIds[$i]) == 0) . "</br>";
			assert(strcmp($steamPersona->getSteamId64(), $expectedIds[$i]) == 0, "Converted SteamId64 is wrong. [Expected: '{$expectedIds[$i]}', Actual: '{$steamPersona->getSteamId64()}'");
		}
		echo "</br>";
	}
	
	validateConversion32To64();
?>
<?php
	require_once '../src/models/SteamPersona.php';
?>

<?php 	
	function testId() {
		$validIds = ['STEAM_0:1:50631306', 'STEAM_0:1:63068168', 'STEAM_0:1:1456069'];
		$invalidIds = ['STEAM_6:1:50631306', 'STEAM_0:10:50631306', 'STEAM_0:1:506313O6'];
		echo "Testing SteamIds</br>";
		foreach($validIds as $validId) {
			echo "Is the SteamId '{$validId}' valid? => " . SteamPersona::isSteamId($validId) . ', expected => 1</br>';
			assert(SteamPersona::isSteamId($validId), "A known valid SteamId was assigned as a valid SteamId."); 
		}
		foreach($invalidIds as $invalidId) {
			echo "Is the SteamId  {$invalidId}' invalid? => " . !SteamPersona::isSteamId($invalidId) . ', expected => 1</br>';
			assert(!(SteamPersona::isSteamId($invalidId)), "A known invalid SteamId was assined as a valid SteamId.");
		}
		echo "</br>";
	}
	
	function testId3() {
		$validIds3 = ['[U:1:101262613]', '[U:1:126136337]', '[U:1:2912139]'];
		$invalidIds3 = ['U:1:101262613', '[U:3:126136337]', '[U:1:2912139O]'];
		echo "Testing SteamIds3</br>";
		foreach($validIds3 as $validId3) {
			echo "Is the SteamId '{$validId3}' valid? => " . SteamPersona::isSteamId3($validId3) . ', expected => 1</br>';
			assert(SteamPersona::isSteamId3($validId3), "A known valid SteamId3 was not assigned as a valid SteamId3.");
		}
		foreach($invalidIds3 as $invalidId3) {
			echo "Is the SteamId  {$invalidId3}' invalid? => " . !SteamPersona::isSteamId3($invalidId3) . ', expected => 1</br>';
			assert(!(SteamPersona::isSteamId3($invalidId3)), "A known invalid SteamId3 was assigned as a valid SteamId3");
		}
		echo "</br>";
	}
	
	testId();
	testId3();
?>
<?php
	require_once('../models/SteamPersona.php');		
	$profile = new SteamPersona($_GET['steamid']);
	if (isset($_GET['debug'])) {
		if ($_GET['debug'] == true) {
			var_dump($profile);	
		}			
	}
?>
<html>
<head>
	<title><?= $profile->getPersonaName(); ?></title>
</head>
<body>
<div id="profile">
	<img src="<?= $profile->getFullAvatar(); ?>" alt="<?= $profile->getPersonaName(); ?>" />
	<h2><a href="<?= $profile->getProfileUrl(); ?>" target="_blank"><?= $profile->getPersonaName(); ?></a></h2>
	<h3>State: <?= $profile->getState(); ?></h3>
</div>
</body>
</html>
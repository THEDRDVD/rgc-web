<?php
	include("settings.php");
	if (empty($_SESSION['steam_uptodate']) or $_SESSION['steam_uptodate'] == false or empty($_SESSION['steamprofile']['steam_personaname'])) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $steamauth['apikey'] . '&steamids=' . $_SESSION['steamid'];
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec($ch);
		curl_close($ch);
		$content = json_decode($result, true);
		$steamprofile = $content['response']['players'][0];
		$_SESSION['steam_uptodate'] = true;
	}
?>

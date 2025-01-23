<?php

require_once(__DIR__ . '/crest.php');

class CRestCurrent extends CRest
{
	protected static $dataExt = [];
	protected static function getSettingData()
	{
		$return = static::expandData(file_get_contents(__DIR__ . '/settings.json'));
		if (is_array($return)) {
			if (!empty(static::$dataExt)) {
				$return['access_token'] = htmlspecialchars(static::$dataExt['AUTH_ID']);
				$return['domain'] = htmlspecialchars(static::$dataExt['DOMAIN']);
				$return['refresh_token'] = htmlspecialchars(static::$dataExt['REFRESH_ID']);
				$return['application_token'] = htmlspecialchars(static::$dataExt['APP_SID']);
			} elseif (!empty($_SESSION['auth_data'])) {
				$return['access_token'] = htmlspecialchars($_SESSION['auth_data']['AUTH_ID']);
				$return['domain'] = htmlspecialchars($_SESSION['auth_data']['DOMAIN']);
				$return['refresh_token'] = htmlspecialchars($_SESSION['auth_data']['REFRESH_ID']);
				$return['application_token'] = htmlspecialchars($_SESSION['auth_data']['APP_SID']);
			} else {
				$return['access_token'] = htmlspecialchars($_REQUEST['AUTH_ID']);
				$return['domain'] = htmlspecialchars($_REQUEST['DOMAIN']);
				$return['refresh_token'] = htmlspecialchars($_REQUEST['REFRESH_ID']);
				$return['application_token'] = htmlspecialchars($_REQUEST['APP_SID']);
			}
		}

		return $return;
	}


	public static function setDataExt($data)
	{
		static::$dataExt = $data;
	}

	public static function saveAuthData($data)
	{		
		$settingsFile = __DIR__ . '/settings.json';
	
		// Check if the settings file exists, if not create it
		if (!file_exists($settingsFile)) {
			// Create an empty JSON file
			file_put_contents($settingsFile, json_encode([]));
		}
	
		// Read the current settings from the file
		$currentSettings = json_decode(file_get_contents($settingsFile), true);
	
		// Merge the new data with the current settings
		$newSettings = array_merge($currentSettings, [
			'access_token' => htmlspecialchars($data['AUTH_ID']),
			'domain' => htmlspecialchars($data['DOMAIN']),
			'refresh_token' => htmlspecialchars($data['REFRESH_ID']),
			'application_token' => htmlspecialchars($data['APP_SID']),
			'client_endpoint' => 'https://' . htmlspecialchars($_REQUEST['DOMAIN']) . '/rest/'
		]);
	
		// Save the new settings back to the file
		file_put_contents($settingsFile, json_encode($newSettings));
	
		// Generate JavaScript to save the new settings to local storage
		echo "<script>
		const authData = {
			access_token: '" . htmlspecialchars($data['AUTH_ID']) . "',
			domain: '" . htmlspecialchars($data['DOMAIN']) . "',
			refresh_token: '" . htmlspecialchars($data['REFRESH_ID']) . "',
			application_token: '" . htmlspecialchars($data['APP_SID']) . "',
			client_endpoint: 'https://" . htmlspecialchars($_REQUEST['DOMAIN']) . "/rest/'
		};
		localStorage.setItem('auth_data', JSON.stringify(authData));
		</script>";
	}	
}

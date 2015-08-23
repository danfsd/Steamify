Steamify
========
This project focus on providing a easy way to get all the information that is relevant about a Steam Profile.
You can instantiate a Steam Profile (aka **Steam Persona**) passing as attribute the user's SteamId32-bit, SteamId64-bit and SteamId3. All necessary SteamId conversions will be made.

Requirements
========
Other than a PHP server installed (such as *Xampp, Wampp, Mamp, Lamp stack*), you will need:
- *PHP cURL*: this extension comes along with almost all PHP Server distributions (such as Xampp, Wampp, etc.) for Windows. If you are in a Linux environment, you can easily install **PHP cURL** by issuing the command **sudo apt-get install php5-curl** into your terminal; 

How to Use
========
At this point, all you need to do is import **/src/models/SteamPersona.php** and **/src/utils/SteamEnums.php** into your project and you are ready to go. Also don't forget to put your Steam API KEY on the **SteamPersona.php** constant **API_KEY**. If you don't have one, go to http://www.steamcommunity.com/dev/apikey and get yours!

**Example Code:**

**1 - Instantiating a SteamPersona with a SteamId64-bit.**
```php
<?php
  $steamPersona = new SteamPersona('76561198024818355');
  echo $steamPersona->getSteamId();   // Will echo 'STEAM_0:1:32276313'
  echo $steamPersona->getSteamId3();  // Will echo '[U:1:64552627]'
  echo $steamPersona->getSteamId64(); // Will echo '76561198024818355'
?>
```

**2 - Instantiating a SteamPersona with a SteamId32-bit.**
```php
<?php
  $steamPersona = new SteamPersona('STEAM_0:1:32276313'); 
  echo $steamPersona->getSteamId();   // Will echo 'STEAM_0:1:32276313'
  echo $steamPersona->getSteamId3();  // Will echo '[U:1:64552627]'
  echo $steamPersona->getSteamId64(); // Will echo '76561198024818355'
?>
```

**3 - Instantiating a SteamPersona with a SteamId3.**
```php
<?php
  $steamPersona = new SteamPersona('[U:1:64552627]'); 
  echo $steamPersona->getSteamId();   // Will echo 'STEAM_0:1:32276313'
  echo $steamPersona->getSteamId3();  // Will echo '[U:1:64552627]'
  echo $steamPersona->getSteamId64(); // Will echo '76561198024818355'
?>
```

There is a PHP file at **/src/controllers/search_player.php** to allow you to quickly fetch a Steam persona.
There is also a couple of test files located at **/tests/** folder. I had never dealt with Unit Testing on PHP and I'll appreciate anyone that can make those tests better.

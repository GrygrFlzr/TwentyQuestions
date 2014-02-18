<?php

// Server config

define('SERVER_NAME', 'tls://irc.esper.net');
define('SERVER_PORT', 6697);
define('SERVER_USESSL',true);

// osu! API config

define('OSU_APIROOT','https://osu.ppy.sh/api/'); // Should stay the same unless Peppy changes it
define('OSU_APIKEY','');                         // Request access at https://osu.ppy.sh/p/api

// Unused visibility flag

define('FLAG_VISIBILE',true);

// Example specific stuff

$admins = array(
    'GrygrFlzr'
);
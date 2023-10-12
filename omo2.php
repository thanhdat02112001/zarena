<?php
require('./bot.php');
$bot2 = new BOT(Envvars::BOT2_NAME, Envvars::BOT2_TOKEN, Envvars::BOARD);
$bot2->main();
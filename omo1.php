<?php
require('./bot.php');
$bot1 = new BOT(Envvars::BOT1_NAME, Envvars::BOT1_TOKEN, Envvars::BOARD);
$bot1->main();

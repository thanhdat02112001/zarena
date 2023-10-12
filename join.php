<?php
require('./bot.php');
$bot1 = new BOT(Envvars::BOT1_NAME, Envvars::BOT1_TOKEN, Envvars::BOARD);
$bot2 = new BOT(Envvars::BOT2_NAME, Envvars::BOT2_TOKEN, Envvars::BOARD);
$bot1->join();
$bot2->join();
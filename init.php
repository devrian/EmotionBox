<?php

use PirloDB\Database;

require_once 'vendor/autoload.php';

$DB = Database::getInstance('localhost', 'root', '', 'emo_db_test');

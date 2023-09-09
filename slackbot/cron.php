<?php
namespace slackbot;

require_once __DIR__ . '/src/Test_Task/Cron.php';
use slackbot\Test_Task\Cron;

set_time_limit(10);
Cron::get_instance()->process();
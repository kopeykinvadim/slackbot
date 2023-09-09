<?php
namespace slackbot;
use slackbot\Test_Task\Integration\Slack;

// Include the Slack class file
require_once __DIR__ . '/src/Test_Task/Integration/Slack.php';

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

// Add a hook to be triggered when an Addon is edited
add_hook('AddonEdit', 1, function($vars) {
    sendMessage($vars, 'hook1_template');
});

// Add a hook to be triggered when an Addon is deleted
add_hook('AddonDeleted', 1, function($vars) {
    sendMessage($vars, 'hook2_template');
});

// Add a hook to be triggered when an Addon is cancelled
add_hook('AddonCancelled', 1, function($vars) {
    sendMessage($vars, 'hook3_template');
});

// Function to send a message using the Slack integration
function sendMessage($vars, $templateSetting) {
    // Create a new Slack object with the specified template setting
    $slack = new Slack($templateSetting);

    // Send the message using the Slack integration
    $slack->send();
}

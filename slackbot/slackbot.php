<?php
// Function to configure the Slack bot module
function slackbot_config() {
    $configarray = [
        'name'        => 'AgentFire Test Task',            // Module name
        'description' => 'AgentFire Test Task',           // Module description
        'version'     => '1.0.0',                         // Module version
        'author'      => '',                              // Module author (you can specify the author's name here)
        'language'    => 'english',                       // Language (default is English)
        'fields'      => [
            'api_key'       => [                            // API Key field configuration
                'FriendlyName' => 'API Key',               // Field label
                'Type'         => 'text',                 // Field type (text input)
                'Size'         => '100',                  // Input size
                'Description'  => 'AgentFire API server key',  // Field description
                'Default'      => '',                     // Default value
            ],
            'slack_channel' => [                            // Slack Channel field configuration
                'FriendlyName' => 'Slack Channel',         // Field label
                'Type'         => 'text',                 // Field type (text input)
                'Size'         => '100',                  // Input size
                'Description'  => 'Channel Name or ID',   // Field description
                'Default'      => '#test',                // Default value
            ],
            'hook1_template' => [                            // Hook 1 Template field configuration
                'FriendlyName' => 'Template for AddonEdit',       // Field label
                'Type'         => 'textarea',             // Field type (textarea input)
                'Rows'         => '5',                    // Number of rows in the textarea
                'Cols'         => '50',                   // Number of columns in the textarea
                'Description'  => 'Template for AddonEdit',  // Field description
                'Default'      => '',  // Default value
            ],
            'hook2_template' => [                            // Hook 1 Template field configuration
                'FriendlyName' => 'Template for AddonDeleted',       // Field label
                'Type'         => 'textarea',             // Field type (textarea input)
                'Rows'         => '5',                    // Number of rows in the textarea
                'Cols'         => '50',                   // Number of columns in the textarea
                'Description'  => 'Template for AddonDeleted)',  // Field description
                'Default'      => '',  // Default value
            ],
            'hook3_template' => [                            // Hook 1 Template field configuration
                'FriendlyName' => 'Template for AddonCancelled',       // Field label
                'Type'         => 'textarea',             // Field type (textarea input)
                'Rows'         => '5',                    // Number of rows in the textarea
                'Cols'         => '50',                   // Number of columns in the textarea
                'Description'  => 'Template for AddonCancelled',  // Field description
                'Default'      => '',  // Default value
            ],
        ],
    ];
    return $configarray;
}

// Function to output the settings form for the Slack bot module
function agentfire_test_task_output($vars) {
    $modulelink = $vars['modulelink'];

    $output = '<h2>AgentFire Test Task</h2>';
    $output .= '<p>Settings Slack Bot.</p>';
    $output .= '<form method="post" action="' . $modulelink . '">
        <label for="api_key">API Key:</label>
        <input type="text" name="api_key" id="api_key" value="' . htmlspecialchars($_POST['api_key']) . '" />
        <br /><br />
        <label for="slack_channel">Slack Channel:</label>
        <input type="text" name="slack_channel" id="slack_channel" value="' . htmlspecialchars($_POST['slack_channel']) . '" />
        <br /><br />

        <input type="submit" name="submit" value="Save" />
    </form>';

    if ($_POST['submit']) {
        $api_key = $_POST['api_key'];
        $slack_channel = $_POST['slack_channel'];
        $output .= '<div class="successbox">Settings successfully saved.</div>';
    }

    echo $output;
}

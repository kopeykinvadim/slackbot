<?php
declare(strict_types=1);

namespace slackbot\Test_Task;

use mysqli;
use slackbot\Test_Task\Traits\Singleton;

require_once __DIR__ . '/Traits/Singleton.php';

/**
 * @package AgentFire\Test_Task
 */
class Cron
{
    use Singleton;

    private $pdo;

    public function queue($params)
    {
        // Your queue function code here...
    }

    public function process()
    {
        // Determine the path to the WHMCS root directory
        $whmcsRoot = realpath(__DIR__ . '/../../../../../');
        $configFilePath = $whmcsRoot . '/configuration.php';

        if (!file_exists($configFilePath)) {
            die('WHMCS configuration file not found.');
        }

        // Include the configuration.php file to access its variables
        include $configFilePath;

        // Create a MySQLi connection using the extracted database credentials
        $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

        if ($mysqli->connect_error) {
            die('Failed to connect to the database: ' . $mysqli->connect_error);
        }

        // Query the slack_message_queue table
        $result = $mysqli->query('SELECT * FROM slack_message_queue');

        // Query the tbladdonmodules table to retrieve the API token
        $query = "SELECT value FROM tbladdonmodules WHERE module = 'slackbot' AND setting = 'api_key'";
        $resultApiKey = $mysqli->query($query);

        if (!$resultApiKey) {
            die('Error executing the query: ' . $mysqli->error);
        }

        // Fetch the API token from the query result
        $row = $resultApiKey->fetch_assoc();
        if (!$row) {
            die('API token not found in tbladdonmodules.');
        }

        $apiToken = $row['value'];
        $url = 'https://slack.com/api/chat.postMessage';

        // Process each message in the slack_message_queue
        while ($message = $result->fetch_assoc()) {
            $data = [
                'channel' => $message['channel'],
                'text' => $message['text'],
            ];
            $data_string = json_encode($data);

            // Use the extracted API token
            $token = $apiToken;

            // Initialize cURL for sending the Slack API request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'Content-Length: ' . strlen($data_string)
            ));

            // Execute the cURL request
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'cURL Error: ' . curl_error($ch);
            }

            // Close the cURL session
            curl_close($ch);

            // Delete the processed message from slack_message_queue
            $deleteQuery = "DELETE FROM slack_message_queue WHERE id = {$message['id']}";
            if (!$mysqli->query($deleteQuery)) {
                die('Error deleting record: ' . $mysqli->error);
            }
        }

        // Close the MySQLi connection
        $mysqli->close();
    }

    public function get_tasks()
    {
        // Your get_tasks function code here...

        return [
            [
                'id'    => 123,
                'title' => 'Updating addon',
            ],
        ];
    }
}

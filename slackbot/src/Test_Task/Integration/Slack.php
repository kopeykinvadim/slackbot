<?php
declare(strict_types=1);
namespace slackbot\Test_Task\Integration;

/**
 * @package slackbot\Test_Task\Integration
 */
class Slack
{
    private $mysqli; // MySQLi database connection
    private $templateSetting; // Stores the template setting

    /**
     * Constructor to initialize the Slack class.
     *
     * @param string $templateSetting - The template setting to use.
     */
    public function __construct(string $templateSetting)
    {
        // Determine the WHMCS root directory and configuration file path
        $whmcsRoot = realpath(__DIR__ . '/../../../../../../');
        $configFilePath = $whmcsRoot . '/configuration.php';

        // Check if the configuration file exists
        if (!file_exists($configFilePath)) {
            die('WHMCS configuration file not found.');
        }

        // Include the configuration.php file to access database credentials
        include $configFilePath;

        // Create a MySQLi connection using database credentials
        $this->mysqli = new \mysqli($db_host, $db_username, $db_password, $db_name);

        // Check for database connection errors
        if ($this->mysqli->connect_error) {
            die('Database connection error: ' . $this->mysqli->connect_error);
        }

        // Find the value of $templateSetting in the tbladdonmodules table
        $this->templateSetting = $this->findTemplateSetting($templateSetting);
    }

    /**
     * Find the value of $templateSetting in the tbladdonmodules table.
     *
     * @param string $hookName - The hook name to search for in the database.
     * @return string - The retrieved template setting.
     */
    private function findTemplateSetting(string $hookName): string
    {
        $query = "SELECT value FROM tbladdonmodules WHERE module = 'slackbot' AND setting = '{$hookName}'";
        $stmt = $this->mysqli->query($query);
        $templateSetting = $stmt->fetch_assoc();

        return !empty($templateSetting) ? $templateSetting['value'] : '';
    }

    /**
     * Send the Slack message.
     */
    public function send()
    {
        $this->createTableIfNotExists();

        $queryChannel = "SELECT setting, value FROM tbladdonmodules WHERE module = 'slackbot' AND setting = 'slack_channel'";
        $stmtChannel = $this->mysqli->query($queryChannel);
        $channelSetting = $stmtChannel->fetch_assoc();
        $channel = !empty($channelSetting) ? $channelSetting['value'] : '';

        if (empty($channel) || empty($this->templateSetting)) {
            echo 'Data not found.';
            return;
        }

        $text = $this->templateSetting;

        $stmt = $this->mysqli->prepare("INSERT INTO slack_message_queue (channel, text) VALUES (?, ?)");
        $stmt->bind_param('ss', $channel, $text);
        $stmt->execute();
    }

    /**
     * Create the table if it doesn't exist.
     */
    private function createTableIfNotExists()
    {
        $tableName = 'slack_message_queue';
        $sql = "
            CREATE TABLE IF NOT EXISTS {$tableName} (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                channel VARCHAR(255) NOT NULL,
                text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        if (!$this->mysqli->query($sql)) {
            // Handle SQL errors
            echo 'SQL Error: ' . $this->mysqli->error;
            exit;
        }
    }
}

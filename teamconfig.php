<?php
// File: team_config.php
// This file can be expanded to include more team configurations
$TEAM_CONFIG = [
    '1610612745' => [
        'name' => 'Houston Rockets',
        'database_connection' => [
            'host' => 'localhost',
            'username' => 'your_username',
            'password' => 'your_password',
            'database' => 'nba_teams'
        ],
        'api_endpoints' => [
            'roster' => 'https://example.com/api/rockets/roster',
            'stats' => 'https://example.com/api/rockets/stats'
        ]
    ]
    // Add more teams as needed
];
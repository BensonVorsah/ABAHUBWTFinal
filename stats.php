<?php
include 'navbar1.php';
require_once 'db_connection.php';

// Function to get player stats for current season
function getPlayerStats($type, $limit = 200) {
    global $conn;
    $orderBy = '';
    
    switch ($type) {
        case 'points':
            $orderBy = 'points DESC';
            break;
        case 'assists':
            $orderBy = 'assists DESC';
            break;
        case 'rebounds':
            $orderBy = 'rebounds DESC';
            break;
        case 'steals':
            $orderBy = 'steals DESC';
            break;
        case 'blocks':
            $orderBy = 'blocks DESC';
            break;
        case 'three_pointers':
            $orderBy = 'three_pointers DESC';
            break;
        case 'free_throws':
            $orderBy = 'free_throws DESC';
            break;
        case 'fantasy_points':
            $orderBy = 'fantasy_points DESC';
            break;
        case 'games_played':
            $orderBy = 'games_played DESC';
            break;
        default:
            $orderBy = 'points DESC';
    }

    $query = "
        SELECT 
            p.player_id, 
            p.Fname, 
            p.Lname, 
            t.team_name,
            ps.points,
            ps.PPG,
            ps.assists,
            ps.APG,
            ps.rebounds,
            ps.RPG,
            ps.steals,
            ps.SPG,
            ps.blocks,
            ps.BPG,
            ps.three_pointers,
            ps.free_throws,
            ps.fantasy_points,
            ps.games_played
        FROM players p
        JOIN playerstats ps ON p.player_id = ps.player_id
        JOIN teams t ON p.team_id = t.team_id
        ORDER BY $orderBy
        LIMIT $limit
    ";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA | League Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .hover-highlight:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 text-blue-700">League Stats</h1>

        <!-- Tab Navigation -->
        <div class="flex justify-center mb-4 flex-wrap">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <?php 
                $statTabs = [
                    'points' => 'Points',
                    'PPG' => 'PPG', 
                    'assists' => 'Assists', 
                    'APG' => 'APG', 
                    'rebounds' => 'Rebounds', 
                    'RPG' => 'RPG', 
                    'steals' => 'Steals', 
                    'SPG' => 'SPG', 
                    'blocks' => 'Blocks', 
                    'BPG' => 'BPG', 
                    'three_pointers' => '3-Pointers', 
                    'free_throws' => 'Free Throws', 
                    'fantasy_points' => 'Fantasy Pts',
                    'games_played' => 'Games Played'
                ];

                $first = true;
                foreach ($statTabs as $type => $label):
                    $roundedClass = $first ? 'rounded-l-lg' : 
                        ($type === 'games_played' ? 'rounded-r-lg' : '');
                    $first = false;
                ?>
                <button type="button" data-tab="<?= $type ?>-tab" class="tab-btn px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 <?= $roundedClass ?> hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tabs Content -->
        <?php 
        foreach ($statTabs as $type => $label): 
        ?>
        <div id="<?= $type ?>-tab" class="tab-content <?= $type === 'points' ? 'active' : '' ?> bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-blue-400 font-bold text-xl">
                <?= $label ?> Leaders
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-400">
                        <tr>
                            <th class="px-4 py-2">Player</th>
                            <th class="px-4 py-2">Team</th>
                            <th class="px-4 py-2">Points</th>
                            <th class="px-4 py-2">PPG</th>
                            <th class="px-4 py-2">Assists</th>
                            <th class="px-4 py-2">APG</th>
                            <th class="px-4 py-2">Rebounds</th>
                            <th class="px-4 py-2">RPG</th>
                            <th class="px-4 py-2">Steals</th>
                            <th class="px-4 py-2">SPG</th>
                            <th class="px-4 py-2">Blocks</th>
                            <th class="px-4 py-2">BPG</th>
                            <th class="px-4 py-2">3-Pts</th>
                            <th class="px-4 py-2">Free Throws</th>
                            <th class="px-4 py-2">Fantasy Pts</th>
                            <th class="px-4 py-2">Games</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $seasonStats = getPlayerStats($type);
                        foreach ($seasonStats as $stat): 
                        ?>
                        <tr class="hover-highlight player-row" data-player-id="<?= $stat['player_id'] ?>">
                            <td class="px-4 py-2"><?= $stat['Fname'] . ' ' . $stat['Lname'] ?></td>
                            <td class="px-4 py-2"><?= $stat['team_name'] ?></td>
                            <td class="px-4 py-2"><?= $stat['points'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['PPG'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['assists'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['APG'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['rebounds'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['RPG'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['steals'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['SPG'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['blocks'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['BPG'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['three_pointers'] ?></td>
                            <td class="px-4 py-2"><?= $stat['free_throws'] ?></td>
                            <td class="px-4 py-2"><?= number_format($stat['fantasy_points'], 1) ?></td>
                            <td class="px-4 py-2"><?= $stat['games_played'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    $(document).ready(function() {
        // Tab switching
        $('.tab-btn').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Hide all tab contents
            $('.tab-content').removeClass('active');
            
            // Show selected tab content
            $('#' + tabId).addClass('active');
            
            // Update active button style
            $('.tab-btn').removeClass('bg-blue-700 text-white');
            $(this).addClass('bg-blue-700 text-white');
        });

        // First tab active by default
        $('.tab-btn[data-tab="points-tab"]').addClass('bg-blue-700 text-white');

        // Player row click to navigate to player details
        $('.player-row').on('click', function() {
            var playerId = $(this).data('player-id');
            window.location.href = 'player_details.php?player_id=' + playerId;
        });
    });
    </script>
</body>
</html>

<?php
include 'footer.php'
?>

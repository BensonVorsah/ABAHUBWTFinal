<?php
include 'navbar1.php';
include 'db_connection.php';
?>

<?php
// media.php - YouTube Playlist Embedding

// YouTube Data API Configuration
// IMPORTANT: You must get a YouTube Data API v3 key from Google Cloud Console
$youtubeApiKey = 'AIzaSyBZlQiRoTbQsTc97ctKpPZgYwb4B0n5e1M';

// Playlist ID for 2024-2025 NBA SEASON from Hooper Highlights
$playlistId = 'PLXlvFN0gKJA5nXpVeX9Uq999DbKREL446';

// Function to fetch playlist items from YouTube
function fetchYouTubePlaylistVideos($apiKey, $playlistId, $maxResults = 24) {
    $apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?" . http_build_query([
        'part' => 'snippet',
        'playlistId' => $playlistId,
        'key' => $apiKey,
        'maxResults' => $maxResults
    ]);

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if(curl_errno($ch)){
        // Log or handle the error
        return [];
    }

    // Close cURL resource
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check if items exist
    $videos = [];
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            $videos[] = [
                'title' => $item['snippet']['title'],
                'video_id' => $item['snippet']['resourceId']['videoId'],
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url']
            ];
        }
    }

    return $videos;
}
?>
<!DOCTYPE html>
<html lang="en" class="lightmode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBA Season Highlights - Hooper Highlights</title>
    <style>

        *::after,
        *::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", sans-serif;
            line-height: 1.6;
            max-width: 2000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0f0f0;
            width: 100%;
        }
        .playlist-header {
            text-align: center;
            background-color: #1a1a1a;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        .video-item {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .video-item:hover {
            transform: scale(1.05);
        }
        .video-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .video-title {
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        .video-embed {
            width: 100%;
            aspect-ratio: 14 / 8;
        }
    </style>
</head>
<body>
    <div class="playlist-header">
        <h1>2024-2025 ABA Season</h1>
        <p>Latest highlights and game recaps from the current ABA season</p>
    </div>

    <div class="video-grid">
        <?php 
        // Fetch videos from the playlist
        $videos = fetchYouTubePlaylistVideos($youtubeApiKey, $playlistId);
        
        foreach($videos as $video): 
        ?>
            <div class="video-item">
                <div class="video-title">
                    <?= htmlspecialchars($video['title']) ?>
                </div>
                <iframe 
                    class="video-embed"
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($video['video_id']) ?>" 
                    title="<?= htmlspecialchars($video['title']) ?>"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>
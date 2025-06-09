<?php
header('Content-Type: application/json');

// Validate input
if (!isset($_POST['username']) || !isset($_POST['searchQuery'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$username = trim($_POST['username']);
$searchQuery = trim($_POST['searchQuery']);

if (empty($username) || empty($searchQuery)) {
    echo json_encode(['error' => 'Username and search query cannot be empty']);
    exit;
}

// Validate username format (Reddit usernames are 3-20 chars, letters, numbers, underscores)
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    echo json_encode(['error' => 'Invalid username format']);
    exit;
}

// In a real implementation, you would call the Reddit API here
// For security reasons, this should be done server-side

// For demo purposes, we'll simulate an API call
try {
    // This is where you would make the actual API call to Reddit
    // $comments = fetchRedditComments($username, $searchQuery);
    
    // For the demo, we'll return some mock data
    $response = [
        'comments' => [] // Will be populated with matching comments
    ];
    
    // In a real implementation, you would:
    // 1. Fetch the user's comments from Reddit API
    // 2. Filter them based on the search query
    // 3. Return the matching comments
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching comments: ' . $e->getMessage()]);
}

// Helper function to actually call Reddit API (example structure)
function fetchRedditComments($username, $searchQuery) {
    $url = "https://www.reddit.com/user/{$username}/comments.json?limit=100";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'RedditCommentSearchTool/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 200) {
        throw new Exception("Reddit API returned status code {$httpCode}");
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['data']['children'])) {
        throw new Exception("Invalid response format from Reddit API");
    }
    
    $comments = [];
    $searchQueryLower = strtolower($searchQuery);
    
    foreach ($data['data']['children'] as $item) {
        if (isset($item['data']['body']) && 
            stripos($item['data']['body'], $searchQuery) !== false) {
            $comments[] = [
                'body' => $item['data']['body'],
                'subreddit' => $item['data']['subreddit_name_prefixed'],
                'score' => $item['data']['score'],
                'created' => $item['data']['created_utc'],
                'permalink' => $item['data']['permalink']
            ];
        }
    }
    
    return $comments;
}
?>

<?php
// Config Section
$apiKey = '';

// Define Variables
$playlistId = '';
$channelId = '';
$maxResults = 20; //Default number of Items requested

if (isset($_GET["maxresults"])){
	$maxResults = $_GET["maxresults"];

	if ($maxResults > 50) {
		$maxResults = 50;
	}
}

if($apiKey == '') {
	die('Please configure the API-Key');
} else if (isset($_GET["playlistid"])) {
	$playlistId = $_GET["playlistid"];

	// API Request for Playlist Properties
	$apiRequest = getdata('https://www.googleapis.com/youtube/v3/playlists?'
			. 'part=snippet'
			. '&id=' . $playlistId
			. '&key=' . $apiKey);
	
	$playlist = json_decode($apiRequest, true);
	
	// DIE if $video is empty
	if(!isset($playlist)){
		die('Could not load Playlist');
	}
	
	// Define Header
	header("Content-Type: application/rss+xml");
	
	// RSS output
	echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n"
		. '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n"
			. '<channel>' . "\n"
				. '<title>' . $playlist["items"][0]["snippet"]["title"] . '</title>' . "\n"
				. '<link>https://www.youtube.com/playlist?list=' . $playlist["items"][0]["id"] . '</link>' . "\n"
				. '<description>' . $playlist["items"][0]["snippet"]["description"] . '</description>' . "\n"
				. '<image>'  . "\n"
					. '<url>'. $playlist["items"][0]["snippet"]["thumbnails"]["medium"]["url"] . '</url>' . "\n"
					. '<title>' . $playlist["items"][0]["snippet"]["title"] . '</title>' . "\n"
					. '<link>https://www.youtube.com/playlist?list=' . $playlist["items"][0]["id"] . '</link>' . "\n"
				. '</image>' . "\n"
				. '<atom:link href="http://'
					. $_SERVER['HTTP_HOST']
					.$_SERVER['PHP_SELF']
					. '?playlistid=' . $playlistId
					.'" rel="self" type="application/rss+xml" />' . "\n";
	
	unset($apiRequest);

	// API Request for Playlist Items
	$apiRequest = getdata('https://www.googleapis.com/youtube/v3/playlistItems?'
			. 'part=snippet'
			. '&playlistId=' . $playlistId
			. '&maxResults=' . $maxResults
			. '&fields=items%2Fsnippet'
			. '&key=' . $apiKey);

	$video = json_decode($apiRequest, true);

	// DIE if $video is empty
	if(!isset($video)){
		die('No Video Data available');
	}
	
	//Schleife durch $video Array beginnend bei items
	foreach($video['items'] as $video_data) {
		echo 	'<item>' . "\n"
					. '<title>' . $video_data['snippet']['title'] .'</title>' . "\n"
					. '<link>https://www.youtube.com/watch?v='
						. $video_data['snippet']['resourceId']['videoId']
					. '</link>' . "\n"
					. '<description></description>' . "\n"
					. '<guid>https://www.youtube.com/watch?v='
						. $video_data['snippet']['resourceId']['videoId']
					. '</guid>' . "\n"
					. '<pubdate>' 
						. date('r', strtotime($video_data['snippet']['publishedAt']))
					. '</pubdate>' . "\n"
				. '</item>' . "\n";
	} // Ende Foreach Schleife

	// RSS schließen
	echo '</channel>' . "\n"
		. '</rss>';

} else if (isset($_GET["channelid"])) {
	$channelId = $_GET["channelid"];

	// API Request for Chanel Properties
	$apiRequest = getdata('https://www.googleapis.com/youtube/v3/channels?'
			. 'part=brandingSettings'
			. '&id=' . $channelId
			. '&key=' . $apiKey);
	
	$channel = json_decode($apiRequest, true);
	
	// DIE if $channel is empty
	if(!isset($channel)){
		die('Could not load Channel Data');
	}
	
	// Define Header
	header("Content-Type: application/rss+xml");
	
	// RSS output
	echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n"
		. '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n"
			. '<channel>' . "\n"
				. '<title>' . $channel["items"][0]["brandingSettings"]["channel"]["title"] . '</title>' . "\n"
				. '<link>https://www.youtube.com/channel/' . $channel["items"][0]["id"] . '</link>' . "\n"
				. '<description>' . $channel["items"][0]["brandingSettings"]["channel"]["description"] . '</description>' . "\n"
				. '<image>'  . "\n"
					. '<url>'. $channel["items"][0]["brandingSettings"]["image"]["bannerImageUrl"] . '</url>' . "\n"
					. '<title>' . $channel["items"][0]["brandingSettings"]["channel"]["title"] . '</title>' . "\n"
					. '<link>https://www.youtube.com/channel/' . $channel["items"][0]["id"] . '</link>' . "\n"
				. '</image>' . "\n"
				. '<atom:link href="http://'
					. $_SERVER['HTTP_HOST']
					.$_SERVER['PHP_SELF']
					. '?channelid=' . $channelId
					.'" rel="self" type="application/rss+xml" />' . "\n";
	
	unset($apiRequest);

	// API Request for Playlist Items
	$apiRequest = getdata('https://www.googleapis.com/youtube/v3/search?'
			. 'part=snippet,id'
			. '&order=date'
			. '&maxResults=' . $maxResults
			. '&channelId=' . $channelId
			. '&key=' . $apiKey);

	$video = json_decode($apiRequest, true);

	// DIE if $video is empty
	if(!isset($video)){
		die('No Video Data available');
	}
	
	//Schleife durch $video Array beginnend bei items
	foreach($video['items'] as $video_data) {
		echo 	'<item>' . "\n"
					. '<title>' . $video_data['snippet']['title'] .'</title>' . "\n"
					. '<link>https://www.youtube.com/watch?v='
						. $video_data['id']['videoId']
					. '</link>' . "\n"
					. '<description>' 
						. $video_data['snippet']['description'] 
					.'</description>' . "\n"
					. '<guid>https://www.youtube.com/watch?v='
						. $video_data['id']['videoId']
					. '</guid>' . "\n"
					. '<pubdate>' 
						. date('r', strtotime($video_data['snippet']['publishedAt']))
					. '</pubdate>' . "\n"
				. '</item>' . "\n";
	} // Ende Foreach Schleife

	// RSS schließen
	echo '</channel>' . "\n"
		. '</rss>';

} else if(!isset($_GET["playlistid"]) && !isset($_GET["channelid"])) {
	die('No Playlist ID (playlistid) or Channel ID (channelid) provided.');
}

// Functions
function getdata($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

?>

<?php header("Content-Type: application/xml");

// Config Section
$apiKey = '';

// Define Variables
$playlistId = '';

if($apiKey == '') {
	die('Please configure the API-Key');
} else if(!isset($_GET["playlistid"])) {
	die('No Playlist ID (playlistid) provided.');
} else {
	$playlistId = $_GET["playlistid"];
}

// API Request for Playlist Properties
$apiRequest = getdata('https://www.googleapis.com/youtube/v3/playlists?'
			. 'part=snippet'
			. '&id=' . $playlistId 
			. '&key=' . $apiKey);

$playlist = json_decode($apiRequest, true);

// DIE if $video is empty
if(!isset($playlist) OR !$playlistId){
	die('Could not load Playlist');
}

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
			. '&maxResults=10'
			. '&fields=items%2Fsnippet'
			. '&key=' . $apiKey);

$video = json_decode($apiRequest, true);

// DIE if $video is empty
if(!isset($video)){
	die('No Video Data available');
}

// Count Videos
if(count($video->items)>0) {
	//Schleife durch $video Array beginnend bei items
	foreach($video['items'] as $video_data) {
		if($video_data['snippet']['title'] != "Private video"){
			echo '<item>' . "\n"
					. '<title>' . $video_data['snippet']['title'] .'</title>' . "\n"
					. '<link>https://www.youtube.com/watch?v=' 
						. $video_data['snippet']['resourceId']['videoId'] 
					. '</link>' . "\n"
					. '<description><![CDATA[' 
						. '<img src="' . $video_data['snippet']['thumbnails']['medium']['url'] . '" '
							. 'alt="' . $video_data['snippet']['title'] . '" '
							. 'width="'. $video_data['snippet']['thumbnails']['medium']['width'] . '" '
							. 'height="' . $video_data['snippet']['thumbnails']['medium']['height'] . '" '
							. '/>' 
						. '<br />' 
						. $video_data['snippet']['description'] 
					. ']]></description>' . "\n"
					. '<guid>https://www.youtube.com/watch?v=' 
						. $video_data['snippet']['resourceId']['videoId'] 
					. '</guid>' . "\n"
					. '<pubDate>' 
						. date(DATE_RFC2822, strtotime($video_data['snippet']['publishedAt']))
					. '</pubDate>' . "\n"
				. '</item>' . "\n"; 
		}
	} // Ende Foreach Schleife
} // End Count Videos

// RSS schließen
echo '</channel>' . "\n"
		. '</rss>';

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

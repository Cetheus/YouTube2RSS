# YouTube2RSS
YouTube2RSS is a quick&dirty Script to convert a YouTube Playlist into a RSS 2.0 Feed-XML Output.

Some of the YouTube API Code is based on a Demo & Template Code from Rico Loschke.
You can visit the Page here: 
http://sevenx.de/blog/tutorial-youtube-channelplaylist-videos-via-data-api-v3-auf-der-eigenen-website-ausgeben-2/

Currently only Playlists and Channels by ID are supported.

## Usage
Add your YouTube-API-Key to the File.
The Playlist-ID has not to be hardcoded.
You have to provide the Playlist-ID via URL:
...?playlistid=[Your YouTube PlayList ID]

Otherwise you can provide a Channel ID:
...?channelid=[Your YouTube Channel ID]

The maximum amount of results can be limited with:
...?maxresults=[1-50]

Please note that due to restrictions in the YouTube API no more than 50 results can be fetched.
This Limit is hardcoded.
## More Documentation will follow

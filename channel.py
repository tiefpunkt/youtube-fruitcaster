#!/usr/bin/python

from apiclient.discovery import build
import json
import os
import subprocess

import config

# Set DEVELOPER_KEY to the "API key" value from the "Access" tab of the
# Google APIs Console http://code.google.com/apis/console#access
# Please ensure that you have enabled the YouTube Data API for your project.
DEVELOPER_KEY = config.DEVELOPER_KEY
YOUTUBE_API_SERVICE_NAME = "youtube"
YOUTUBE_API_VERSION = "v3"

DIR_DATA = "data"
DIR_METADATA = DIR_DATA + "/meta"
DIR_VIDEOS = DIR_DATA + "/videos"

YOUTUBE_DL = "/home/severin/bin/youtube-dl"

def youtube_search():
	search_request = youtube.search().list(
		#q="raumzeitlabor",
		channelId="UCG0GYTzLZNbITXIO63mjWlQ",
		part="id",
		maxResults=50
	)
	search_response = search_request.execute()

	videos = []

	for search_result in search_response.get("items", []):
		if search_result["id"]["kind"] == "youtube#video":
			videos.append(search_result["id"]["videoId"])
			
	metadata_collection = []
	
	gather_metadata(videos, metadata_collection)
	
	youtube_search_next(search_request, search_response, metadata_collection)
																 
	return metadata_collection

def youtube_search_next(request, response, metadata_collection):
	next_request = youtube.search().list_next(request, response)
	if next_request:
		next_response = next_request.execute()
		
		videos = []
		
		for search_result in next_response.get("items", []):
			if search_result["id"]["kind"] == "youtube#video":
				videos.append(search_result["id"]["videoId"])
				
		gather_metadata(videos, metadata_collection)
		youtube_search_next(next_request, next_response, metadata_collection)
	
def gather_metadata(videos, metadata_collection):
	video_details = youtube.videos().list(id=",".join(videos), part="snippet,contentDetails").execute()

	for video in video_details.get("items", []):
		metadata = {}
		metadata["id"] = video["id"]
		metadata["title"] = video["snippet"]["title"]
		metadata["description"] = video["snippet"]["description"]
		metadata["publishedAt"] = video["snippet"]["publishedAt"]
		metadata["duration"] = video["contentDetails"]["duration"]
		metadata_collection.append(metadata)
	
def write_metadata(video):
	if not os.path.isfile(DIR_METADATA + "/" + video["id"] + ".json"):
		f = open(DIR_METADATA + "/" + video["id"] + ".json", mode = "w")
		f.write(json.dumps(video))
		f.close()
	
def check_video(video):
	if not os.path.isfile(DIR_METADATA + "/" + video["id"] + ".mp4"):
		subprocess.call([YOUTUBE_DL, "-o" + DIR_VIDEOS + "/" + video["id"] + ".mp4", "http://youtu.be/" + video["id"]])

youtube = build(YOUTUBE_API_SERVICE_NAME, YOUTUBE_API_VERSION,
		developerKey=DEVELOPER_KEY)
		
videos = youtube_search()

for video in videos:
	write_metadata(video)
	check_video(video)

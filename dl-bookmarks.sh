#!/bin/bash
declare -a SOURCES 
readarray -t SOURCES < /volume1/service/download/bookmarks/sources/source_xxx.txt 
for ((i = 0; i < ${#SOURCES[@]}; i++)); do
	SOURCELINE=${SOURCES[$i]}
	IFS='|' 
	read -r -a LINEITEMS <<< "$SOURCELINE"
	STOREPATH=${LINEITEMS[0]}
	URL=${LINEITEMS[1]}
	FILENAME="/volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/$STOREPATH/%(upload_date)s - %(title)s - (%(duration)ss) [%(resolution)s] [%(id)s].%(ext)s"
	youtube-dl -o "$FILENAME" --config-location /volume1/service/download/bookmarks/config/xxx_bookmarks.conf "$URL"
done

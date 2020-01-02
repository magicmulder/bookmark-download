#!/usr/bin/php
<?php
/**
*	Chrome Bookmark Downloader - Source Generator
*
*	1.3.1
*
*	(c) 2018/19 magicmulder <code@adorable-illusion.com>
*
*/


$MAXDEPTH = 10;
$INDENT_OFFSET = 12;  

$bookmarkFileInput = '/volume1/service/download/bookmarks/sources/bookmarks_xxx.html';
$bookmarkSourceOutput = '/volume1/service/download/bookmarks/sources/source_xxx.txt';
$bookmarkArchive = '/volume1/service/download/bookmarks/archives/xxx_bookmarks_archive_links.txt';
if (isset($argv[1])) {
	$bookmarkFile = $argv[1];
}

$handle = fopen($bookmarkFileInput, 'r');
$previousDownloads = (file_exists($bookmarkArchive)?file($bookmarkArchive):array());
$handle2 = fopen($bookmarkArchive, 'a');
fwrite($handle2, '--- ' . date('d.m.Y') . ' ---' . chr(10));
$handle3 = fopen($bookmarkSourceOutput, 'w');
$count = 0;
$linecount = 0;
for ($i = 0; $i <= $MAXDEPTH; $i++) {
	$level[$i] = '';
}
while (($line = fgets($handle)) !== false) {
	$linecount++;
	/* Auto determine offset of first entry */
	if ($linecount == 1) {
		preg_match_all('/(.+)<DT>/iU', $line, $indentMatch);
		$leadingSpacesMatch = $indentMatch[1][0];
		$INDENT_OFFSET = strlen($leadingSpacesMatch);
	}
	echo ".";
	$regExpLabel = '/(.+)<DT><H3 ADD_DATE="(.+)" LAST_MODIFIED="(.+)">(.+)<\/H3>/iU';
	$regExpUrl = '/(.+)<A HREF="(.+)"/iU';
	preg_match_all($regExpLabel, $line, $match);
	if (isset($match[4][0])) {
		$indent = $match[1][0];
		$label = $match[4][0];
		$label = trim(preg_replace('/[^0-9A-Za-z _]/i', '', $label));
		$label = str_replace('quot', '', $label);
		$currLevel = (strlen($indent)-$INDENT_OFFSET)/4;
		$level[$currLevel] = $label;
		for ($i = 1; $i <= $MAXDEPTH; $i++) {
			$level[$currLevel+$i] = '';
		}
	} else {
		preg_match_all($regExpUrl, $line, $match2);
		if (isset($match2[2][0])) {
			$link = $match2[2][0];
			$currPath = $level[0];
			for ($i = 1; $i <= $MAXDEPTH; $i++) {
				if ($level[$i] != '') $currPath .= '/' . $level[$i];
			}
			$sourceItem = $currPath . '|' . $link;
			/* If this bookmark does not yet appear in the archive, 
				write it to output file and archive.
			*/
			if (!in_array($sourceItem . chr(10), $previousDownloads)) {
				echo "X";
				$count++;
				fwrite($handle3, $sourceItem . chr(10));
				fwrite($handle2, $sourceItem . chr(10));
			}
		}
	}	
}
fclose($handle);
fclose($handle3);
                    

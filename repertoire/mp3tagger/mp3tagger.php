<?php

require_once('getid3/getid3.php');
require_once('getid3/write.php');

class mp3tagger {
	
	private $getID3;
	private $tagwriter;
	private $tagdata;
	
	private $debug = true;
	
	private $rename;
	public $filename;
	
	function __construct() { //$rename = false) {
		
		$this->getID3 = new getID3;
		$this->getID3->setOption(array('encoding'=>'UTF-8'));
		
		$this->tagwriter = new getid3_writetags;		
		$this->tagwriter->tagformats = array('id3v1', 'id3v2.3');
		$this->tagwriter->overwrite_tags = true;
		$this->tagwriter->tag_encoding   = 'UTF-8';
		$this->tagwriter->remove_other_tags = true;
		
		//$this->rename = $rename;
	}
	
	
	
	public function write($file, $title, $artist, $album, $album_artist = false, $picture = false, &$duration = false) {
		
		$this->tagwriter->filename = $file;//$this->filename = 
		
		if (in_array(pathinfo($file, PATHINFO_EXTENSION), array('mp3'))) {
			
			$album_artist = empty($album_artist) ? $artist : $album_artist;
			
			if (in_array($album, array('Practice', 'Singalong'))) { //, 'Original Recording'
				$album = "LOW REZ $album";
			}
			
			$this->tagdata = array(
				'title'   => array($title),
				'artist'  => array($artist),
				'album'   => array($album),
				'band'    => array($album_artist)
			);
			
			$this->image($picture);
			
			$this->tagwriter->tag_data = $this->tagdata;
			
			if ($this->tagwriter->WriteTags()) {
				if ($this->debug && !empty($this->tagwriter->warnings)) {
					echo 'There were some warnings:<br>'.implode('<br><br>', $this->tagwriter->warnings);
				}
				
				//if ($this->rename) 
				//$this->filename = $this->rename($file, $title, $artist, $album);

				try {
					$this->getID3->analyze($this->tagwriter->filename);
					$duration = @$this->getID3->info['playtime_string'];
				}
				catch (Exception $e) {
					echo 'An error occured: ' . $e->message;
				}
				
				return true;
				
			} else {
				if ($this->debug) echo 'Failed to write tags!<br>'.implode('<br><br>', $this->tagwriter->errors);
				return false;
			}
			
		}
		
		//if ($this->rename) 
		//	$this->filename = $this->rename($file, $title, $artist, $album);
		
		return $true;
		
	}
	
	public function rename($file, $title, $artist, $album, $basepath=false) {
		$oldfile = $file;// $this->filename;
		
		$ext = pathinfo($oldfile, PATHINFO_EXTENSION);
		
		if ($album == 'Original Recording') {
			$artist_file = $artist;
		}
		elseif (is_array($artist)) {
			$artist_path = array_shift($artist);
			$artist_file = array_pop($artist);
		}
		else {
			$artist_file = $artist;
			$artist_path = $artist;
		}
		
		if ($album == 'Original Recording') {
			$newfile = $this->safe_file_name("{$title} - {$artist_file} - {$album}");
			$newpath = $this->safe_file_path($album);
		}
		else {
			$newfile = $this->safe_file_name("{$title} - {$album} - {$artist_file}");
			$newpath = $this->safe_file_path($album.'/'.$artist_path);
		}
		
		$newpath .='/';
		
		if (empty($basepath)) { $basepath = pathinfo($oldfile, PATHINFO_DIRNAME); }
		
		if ($newpath && !is_dir("{$basepath}/{$newpath}")) {
			if (!mkdir("{$basepath}/{$newpath}", 0777, true)) {
				$newpath = false;
			}
		}
		
		if (is_file($oldfile)) {
			if ( $oldfile != "{$basepath}/{$newpath}{$newfile}{$i}.{$ext}") {
				$i = false;
				while (file_exists("{$basepath}/{$newpath}{$newfile}{$i}.{$ext}")) {
					if ($i) { $i = ' ('.(((int) trim($i, ' ()')) + 1).')'; }
					else { $i = ' (2)'; }
				};
				
				if (rename($oldfile, "{$basepath}/{$newpath}{$newfile}{$i}.{$ext}")) {
					return "{$newpath}{$newfile}{$i}.{$ext}";
				}
			}
		}
		
		return $oldfile;
		
	}
	
	private function safe_file_path ( $filename ) {
		
		$filename = str_replace('(', '//', trim($filename, ')'));
		$paths = preg_split('%(/|\\\\)+%i', rtrim($filename, '/\\'));
		
		foreach ($paths as &$path) {
			$path = $this->safe_file_name($path, true);
		}
		
		return implode(DIRECTORY_SEPARATOR, $paths);
		
	}
	
	private function safe_file_name( $filename, $path = false ) {
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "|", "~", "`", "!", "{", "}", chr(0));//"(", ")", 
		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/\s+/', ' ', $filename);
		$filename = preg_replace('/[\s-]+$/', '', $filename);
		
		if ($path) {
			$filename = preg_replace('/[\s-]+/', '-', $filename);
			$filename = strtolower($filename);
		}
		
		$filename = trim($filename, '.-_ ');
		return $filename;
	}
	
	private function image($picture_file) {
		//if (file_exists($picture_file)) {
		if ($fd = fopen($picture_file, 'rb')) {
			
			$APICdata = fread($fd, filesize($picture_file));
			fclose ($fd);
			
			list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($picture_file);
			
			$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
			
			if (isset($imagetypes[$APIC_imageTypeID])) {
				$this->tagdata['attached_picture'][0]['data']          = $APICdata;
				$this->tagdata['attached_picture'][0]['picturetypeid'] = '0';//$_POST['APICpictureType'];
				$this->tagdata['attached_picture'][0]['description']   = 'LOW REZ';
				$this->tagdata['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];		
			}
		}
		//}
	}
}


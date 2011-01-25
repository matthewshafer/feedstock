<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Serves up files from the files folder.  It is really basic and hasn't been tested
 * 
 */
class FileServe
{
	protected $database;
	protected $router;
	protected $fileLoc;
	protected $downloadEnabled;
	protected $fileDownloadSpeed = -1;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $database
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($database, $router, $baseLocation, $enabled)
	{
		$this->database = $database;
		$this->router = $router;
		$this->downloadEnabled = $enabled;
		
		$this->fileLoc = $baseLocation . "/private/files/" . $this->router->getUriPosition(2);
	}
	
	/**
	 * render function.
	 * 
	 * @brief Sets headers and sends the file at the speed set in config.php
	 * @access public
	 * @return void
	 */
	public function render()
	{
		$text = null;
		
		
		if($this->downloadEnabled)
		{
			if(file_exists($this->fileLoc) && is_file($this->fileLoc))
			{
		
				header('Content-Description: File Transfer');
				header('Content-Type: ' . $this->getContentType());
				header('Content-Length: ' . filesize($this->fileLoc));
			
				// possibly some checks to see if the file should be shown in the browser or downloaded
			
				// downloaded
				header('Content-Disposition: attachment; filename=' . basename($this->fileLoc));
			
				// view in browser
				//header('Content-Disposition: inline; filename=' . basename($file));
			
				// does the reading of the file
				//readfile($this->fileLoc);
			
				$file = fopen($this->fileLoc, "r");
				
				while(!feof($file))
				{
					if($this->fileDownloadSpeed <= 0)
					{
						print fread($file, round($this->fileDownloadSpeed * 1024));
						sleep(1);
					}
					else
					{
						print fread($file, 2048);
					}
					
					// flushes the stuff from output buffer but doesnt allow output buffer to save the data
					ob_flush();
					flush();
				}
				
				fclose($file);
			
			}
			else
			{
				$text =  "File does not exist";
			}
		}
		else
		{
			$text = "File serving is disabled";
		}
		
		return $text;
		
	}
	
	/**
	 * getContentType function.
	 * 
	 * @brief figures out the content type else uses the default file type
	 * @access protected
	 * @return String
	 */
	protected function getContentType()
	{
		$return = null;
		
		$array = array(
			"aiff" => "audio/x-aiff",
			"asf" => "video/x-ms-asf",
			"asm" => "text/x-asm",
			"asp" => "text/asp",
			"asx" => "video/x-ms-asf",
			"avi" => "video/avi",
			"bmp" => "image/bmp",
			"bz" => "application/x-bzip",
			"bz2" => "application/x-bzip2",
			"c" => "text/plain",
			"c" => "text/x-c",
			"c++" => "text/plain",
			"cc" => "text/plain",
			"cco" => "application/x-cocoa",
			"class" => "application/java",
			"conf" => "text/plain",
			"cpp" => "text/x-c",
			"csh" => "application/x-csh",
			"css" => "text/css",
			"def" => "text/plain",
			"doc" => "application/msword",
			"gz" => "application/x-gzip",
			"gzip" => "application/x-gzip",
			"h" => "text/plain",
			"hqx" => "application/binhex",
			"htm" => "text/html",
			"html" => "text/html",
			"htmls" => "text/html",
			"imap" => "application/x-httpd-imap",
			"jav" => "text/plain",
			"java" => "text/plain",
			"jpeg" => "image/jpeg",
			"jpg" => "image/jpeg",
			"js" => "application/x-javascript",
			"latex" => "application/x-latex",
			"log" => "text/plain",
			"lsp" => "application/x-lisp",
			"lst" => "text/plain",
			"m" => "text/plain",
			"m1v" => "video/mpeg",
			"m2a" => "audio/mpeg",
			"m2v" => "video/mpeg",
			"m3u" => "audio/x-mpequrl",
			"man" => "application/x-troff-man",
			"mht" => "message/rfc822",
			"mhtml" => "message/rfc822",
			"midi" => "audio/midi",
			"mime" => "message/rfc822",
			"mjpg" => "video/x-motion-jpeg",
			"mm" => "application/base64",
			"mme" => "application/base64",
			"mov" => "video/quicktime",
			"movie" => "video/x-sgi-movie",
			"mp3" => "audio/mpeg3",
			"mpa" => "audio/mpeg",
			"mpeg" => "video/mpeg",
			"mpg" => "video/mpeg",
			"part" => "application/pro_eng",
			"pas" => "text/pascal",
			"pbm" => "image/x-portable-bitmap",
			"pct" => "image/x-pict",
			"pcx" => "image/x-pcx",
			"pdb" => "chemical/x-pdb",
			"pdf" => "application/pdf",
			"pkg" => "application/x-newton-compatible-pkg",
			"pl" => "text/plain",
			"ppt" => "application/mspowerpoint",
			"py" => "text/x-script.phyton",
			"pyc" => "applicaiton/x-bytecode.python",
			"qt" => "video/quicktime",
			"rtf" => "text/richtext",
			"rtx" => "text/richtext",
			"sh" => "text/x-script.sh",
			"shtml" => "text/html",
			"swf" => "application/x-shockwave-flash",
			"tcsh" => "text/x-script.tcsh",
			"text" => "text/plain",
			"tgz" => "application/x-compressed",
			"tiff" => "image/tiff",
			"txt" => "text/plain",
			"vcs" => "text/x-vcalendar",
			"wav" => "audio/wav",
			"wax" => "audio/x-ms-wax",
			"wma" => "audio/x-ms-wma",
			"wmv" => "audio/x-ms-wmv",
			"xls" => "application/excel",
			"xml" => "text/xml",
			"zip" => "application/x-compressed",
			"zsh" => "text/x-script.zsh"
		);
		
		$ext = pathinfo($this->fileLoc, PATHINFO_EXTENSION);
		
		if(isset($array[$ext]))
		{
			$return = $array[$ext];
		}
		else
		{
			$return = "application/octet-stream";
		}
		
		return $return;
		
	}
	
	public function setDownloadSpeed($speed)
	{
		$this->fileDownloadSpeed = intval($speed);
	}

}
?>
<?php
/**
 * Connections supported by PHPs Curl-functions
 * 
 * @author Tuomas Angervuori <tuomas.angervuori@gmail.com>
 * @author Baki Goxhaj <banago@gmail.com>
 * @note This backend hasn't been properly tested...
 * @license http://opensource.org/licenses/LGPL-3.0 LGPL v3
 */

namespace Banago\Bridge\Backend;

use Banago\Bridge\Backend;

class Curl implements Backend {
	
	protected $conn;
	protected $url;
	protected $parsedUrl;
	protected $path;
	protected $dir;
	
	/**
	 * Establish a connection
	 */
	public function __construct($url, array $options = null) {
		
		$this->url = $url;
		$this->parsedUrl = parse_url($this->url);
		
		$host = $this->parsedUrl['host'];
		
		$this->conn = curl_init($url);
		if($this->conn === false) {
			throw new \Bridge\Exception("Could not connect to '$host': " . curl_error($this->conn));
		}
		
		if(isset($this->parsedUrl['user']) && $this->parsedUrl['user']) {
			$user = urldecode($this->parsedUrl['user']);
			if($this->parsedUrl['pass']) {
				$pass = urldecode($this->parsedUrl['pass']);
				$login = "$user:$pass";
			}
			else {
				$login = $user;
			}
			if(!curl_setopt($this->conn, \CURLOPT_USERPWD, $login)) {
				throw new \Bridge\Exception("Login to '$host' as '$user' failed");
			}
		}
		
		$userAgent = 'AngerBrowser (cURL/PHP)';
		if($options) {
			foreach($options as $name => $value) {
				//User agent string (http)
				if($name == 'userAgent') {
					$userAgent = $value;
				}
				//Use proxy
				else if($name == 'proxy') {
					curl_setopt($this->conn, \CURLOPT_PROXY, $value);
				}
				//File to store cookies
				else if($name == 'cookiefile') {
					curl_setopt($this->conn, \CURLOPT_COOKIEFILE, $value);
					curl_setopt($this->conn, \CURLOPT_COOKIEJAR, $value);
				}
				//Download headers (http)
				else if($name == 'getHeaders') {
					curl_setopt($this->conn, \CURLOPT_HEADER, $value);
				}
				else {
					trigger_error("Unknown option '$name'", \E_USER_NOTICE);
				}
			}
		}
		curl_setopt($this->conn, \CURLOPT_USERAGENT, $userAgent);
	}
	
	public function __destruct() {
		if($this->conn) {
			curl_close($this->conn);
		}
	}
	
	/**
	 * Change directory
	 */
	public function cd($directory) {
		throw new \Bridge\Exception("Unable to change directory with cURL backend");
		return true;
	}

	/**
	 * Print working directory
	 */
	public function pwd() {
		throw new \Bridge\Exception("Unable to print working directory");
		return true;
	}

	/**
	 * Download a file 
	 */
	public function get($remoteFile) {
		curl_setopt($this->conn, \CURLOPT_RETURNTRANSFER, true);
		
		$url = $this->parsedUrl['scheme'] . '://' . $this->parsedUrl['host'] . '/';
		if(isset($this->parsedUrl['path'])) {
			$url .= $this->parsedUrl['path'] . '/';
		}
		$url .= $remoteFile;
		curl_setopt($this->conn, \CURLOPT_URL, $url);
		
		$data = curl_exec($this->conn);
		if($data === false) {
			throw new \Bridge\Exception("Could not download file '$remoteFile: " . curl_error($this->conn));
		}
		
		//Change back to the default url
		curl_setopt($this->conn, \CURLOPT_URL, $this->url);
		
		return $data;
	}
	
	/**
	 * Upload a file 
	 */
	public function put($data, $remoteFile) {
		$fp = tmpfile();
		fwrite($fp,$data);
		fseek($fp,0);
		
		$url = $this->parsedUrl['scheme'] . '://' . $this->parsedUrl['host'] . '/';
		if(isset($this->parsedUrl['path'])) {
			$url .= $this->parsedUrl['path'] . '/';
		}
		$url .= $remoteFile;
		
		curl_setopt($this->conn, \CURLOPT_UPLOAD, true);
		curl_setopt($this->conn, \CURLOPT_INFILE, $fp);
		curl_setopt($this->conn, \CURLOPT_URL, $url);
		//curl_setopt($this->conn, CURLOPT_INFILESIZE, filesize($fileName));
		if(curl_exec($this->conn) === false) {
			throw new \Bridge\Exception("Could not upload file '$remoteFile: " . curl_error($this->conn));
		}
		
		fclose($fp);
	}
	
	
	/**
	 * List current directory
	 */
	public function ls() {
		throw new \Bridge\Exception("Unable to list files using cURL backend");
	}

	/**
	 * File or directory exists
	 */
	public function exists($path) {
		throw new \Bridge\Exception("Unable to check");
    }	

	/**
	 * Delete a file from remote server
	 */
	public function rm($remoteFile) {
		throw new \Bridge\Exception("Unable to remove files using cURL backend");
	}
	
	/**
	 * Rename file in remote server
	 */
	public function mv($remoteFile, $newName) {
		throw new \Bridge\Exception("Unable to rename files using cURL backend");
	}
	
	/**
	 * Create a directory in remote server
	 */
	public function mkdir($dirName) {
		throw new \Bridge\Exception("Unable to create directories using cURL backend");
	}
	
	/**
	 * Remove a directory from remote server
	 */
	public function rmdir($dirName) {
		throw new \Bridge\Exception("Unable to remove directories using cURL backend");
	}
	
	/**
	 * Return array of supported protocols
	 */
	public static function getAvailableProtocols() {
		if(function_exists('curl_version')) {
			$curlData = curl_version();
			return $curlData['protocols'];
		}
		else {
			return array();
		}
	}
}

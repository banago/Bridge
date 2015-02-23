<?php
/**
 * Establish a connection to a server using different PHP backends
 * 
 * @author Tuomas Angervuori <tuomas.angervuori@gmail.com>
 * @author Baki Goxhaj <banago@gmail.com>
 * @link http://wplancer.com
 * @version 1.0.9
 * @license http://opensource.org/licenses/LGPL-3.0 LGPL v3
 */
 
namespace Banago\Bridge;

use Banago\Bridge\Backend;
use Banago\Bridge\Backend\Ftp;
use Banago\Bridge\Backend\Ssh2;
use Banago\Bridge\Backend\Curl;

class Bridge {
	
	protected $backend;
	
	public function __construct($url, array $options = null) {
		
		$urlParts = parse_url($url);
		
		if(!isset($urlParts['scheme'])) {
			throw new \Exception('Scheme not defined in '.$url);
		}
		
		$scheme = strtolower($urlParts['scheme']);
		
		//Primary option, try to use ssh2 functions as backend
		if(in_array($scheme, Ssh2::getAvailableProtocols())) {
			if(Ssh2::isSupported()) {
				$this->backend = new Ssh2($url, $options);
			} else {
				throw new \Exception("ssh2 PECL extension is not installed. Please install it to use ssh.");
			}
		}
		//Secondary option, try to use ftp functions as backend
		else if(in_array($scheme, Ftp::getAvailableProtocols())) {
			$this->backend = new Ftp($url, $options);
		}
		//Third option, use curl functions as backend
		else if(in_array($scheme, Curl::getAvailableProtocols())) {
			$this->backend = new Curl($url, $options);
		}
		else {
			throw new \Exception("Unsupported protocol '{$urlParts['scheme']}'");
		}
	}
	
	/**
	 * Change directory
	 */
	public function cd($directory) {
        return $this->backend->cd($directory);
	}
	
	/**
	 * Change directory
	 */
	public function pwd() {
	   return $this->backend->pwd();
	}
		
	/**
	 * Download a file 
	 */
	public function get($remoteFile) {
		return $this->backend->get($remoteFile);
	}
	
	/**
	 * Upload a file 
	 */
	public function put($data, $remoteFile) {
		return $this->backend->put($data, $remoteFile);
	}
	
	/**
	 * List current directory
	 */
	public function ls() {
		return $this->backend->ls();
	}

	/**
	 * File or directory exists
	 */
	public function exists($path) {
		return $this->backend->exists($path);
	}
	
	
	/**
	 * Delete a file from remote server
	 */
	public function rm($remoteFile) {
		return $this->backend->rm($remoteFile);
	}
	
	/**
	 * Rename file in remote server
	 */
	public function mv($remoteFile, $newName) {
		return $this->backend->mv($remoteFile, $newName);
	}
	
	/**
	 * Create a directory in remote server
	 */
	public function mkdir($dirName) {
		return $this->backend->mkdir($dirName);
	}
	
	/**
	 * Remove a directory from remote server
	 */
	public function rmdir($dirName) {
		return $this->backend->rmdir($dirName);
	}
	
	/**
	 * Return array of supported protocols
	 */
	public static function getAvailableProtocols() {
		$protocols = array_merge(Ssh2::getAvailableProtocols(), Ftp::getAvailableProtocols(), Curl::getAvailableProtocols());
		$protocols = array_unique($protocols);
		return $protocols;
	}
		
}

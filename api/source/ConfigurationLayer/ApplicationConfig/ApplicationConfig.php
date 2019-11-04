<?php

namespace ConfigurationLayer\ApplicationConfig;

use Brite\Config\IniConfig;
use ConfigurationLayer\ConfigurationAnnotations\IConfigurationAnnotations;

class ApplicationConfig{

	protected $config;

	/**
	 * @var \ConfigurationLayer\ConfigurationAnnotations\IConfigurationAnnotations
	 */
	protected $configurationAnnotations;

	public function __construct($filePath, $environment){
		if (file_exists($filePath)){
			$this->config = new IniConfig($filePath, $environment);
		}else{
			throw new EConfigurationMissing();
		}
	}

	public function setAnnotations(IConfigurationAnnotations $annotations){
		$this->configurationAnnotations = $annotations;
	}

	public function get($keyPath){
		if (getenv($keyPath)){
			return getenv($keyPath);
		}else{
			$originalValue = $this->config->get($keyPath);
			if (isset($this->configurationAnnotations)){
				return $this->configurationAnnotations->parseString($originalValue);
			}
			return $originalValue;
		}
	}

	public function getCertificateAuthorityChain(){
		return ROOT_PATH . "/private/crypto/certificate-authority/cacert.pem";
	}

}
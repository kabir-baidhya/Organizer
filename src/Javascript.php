<?php 

namespace Gckabir\Organizer;

use Gckabir\Organizer\AwesomeCache\CacheData;

class Javascript extends OrganizerObject {

	protected $variables = array();
	protected $extension = '.js';
	protected $version = '1.0';


	public function __construct($bundle, array $scripts, $version)
	{
		parent::__construct($bundle, $scripts, $version);
	}

	/**
	 * Initialize dynamic variables (in bulk)
	 */
	public function vars(array $variables)
	{
		$this->variables = $variables;
		return $this;
	}

	/**
	 * Sets a value of dynamic variable
	 */
	public function setVar($key, $value) {
		$this->variables[$key]	 = $value;
		return $this;
	}

	public function output() {
		$javascript = $this->signature();
		
		# variables
		if($this->config['useStrict'] == true) {
			$javascript .= "'use strict';\n";
		}

		$wrapCode = $this->config['wrap'];
		$dependencies = $this->config['dependencies'];

		$variablesJson = json_encode( (object) $this->variables );

		if($wrapCode) {

			$token= 'function';
			$lBrace = '{ ';
			$rBrace = '}';
			$lBracket = chr(40);
			$rBracket = chr(41);

			if(is_string($dependencies)) {
				$dependencies = explode(',', trim($dependencies));
			}

			$dependencies['$vars'] = $variablesJson;

			$parameters = array();
			$arguments = array();

			
			foreach ($dependencies as $key => $value) {
				if(is_numeric($key)) {
					$parameters[] = $value;
					$arguments[] = $value;
				} else {
					$parameters[] = $key;
					$arguments[] = $value;	
				}
			}

			$parameterString = implode(',', $parameters);
			$argumentString = implode(',', $arguments);
			

			$javascript .= $lBracket.$token.$lBracket.$parameterString.$rBracket.$lBrace."\n";

		} else {

			$javascript .= 'var $vars = '. $variablesJson .";\n";

		}
		
		$javascript .= $this->output."\n";

		if($wrapCode) {
			$javascript .= $rBrace.$rBracket.$lBracket.$argumentString.$rBracket;
		}

		return $javascript;
	}

	public function outputMinified()
	{
		return $this->signature(). \JShrink\Minifier::minify($this->output());	
	}

	public function includeHere() {
		echo '<script type="text/javascript" src="'.$this->build().'" ></script>';
	}

	public function embedHere() {
		$content = $this->preEmbedContent();
		echo '<script type="text/javascript">'.$content.'</script>';
	}	

	
}


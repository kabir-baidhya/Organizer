<?php 

namespace Gckabir\Organizer;


class Css extends OrganizerObject implements IVariableContainer {

	protected $variables = array();
	protected $extension = '.css';
	private $urlPattern = '(url\s*\(\s*[\'\"]?)((?![a-z]+:\/\/)[^\)\'\"\s]+)([\'\"]?\s*\))';

	public function __construct($bundle, array $styles, $version)
	{
		parent::__construct($bundle, $styles, $version);
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
	
	public function output()
	{
		return $this->signature().$this->output;
	}

	public function outputMinified()
	{
		return $this->signature().\CssMin::minify($this->output());
	}

	public function includeHere() {
		echo '<link rel="stylesheet" type="text/css" href="'.$this->build().'" >';
	}

	public function embedHere() {
		$content = $this->preEmbedContent();
		echo '<style type="text/css">'.$content.'</style>';
	}

	private function processVariables() {
		$this->output = preg_replace_callback('/\[\$([a-z][a-z0-9_]*)\]/i', array($this,'callbackVariableReplace'), $this->output);
	}

	private function callbackVariableReplace(array $matches) {
		// $this->variables = $this->variables;
		$identifier = $matches[1];

		$value = isset($this->variables[$identifier]) ? $this->variables[$identifier] : null;
		return $value;
	}



	public function merge () {
		parent::merge();

		# variables
		$this->processVariables();
		
		
	}

	private function fixRelativeUrls($filepath, $content) {

		$baseDirectory = dirname($filepath);

		$pattern = '/'.$this->urlPattern.'/i';

		# if being served from custom serverUri then go back to root first
		$customServerUri = trim(OZR::getConfig('serverUri'), '/');

		$slashCount = substr_count($customServerUri, '/');

		$backToRoot = str_repeat('../', $slashCount);

		$baseDirectory = $backToRoot.rtrim($baseDirectory, '/').'/';
		$content = preg_replace($pattern, '${1}'.$baseDirectory.'$2$3', $content);

		return $content;
	}

	private function removeImports($filepath, $code) {
		
		$baseDirectory = rtrim(dirname($filepath), '/').'/';
		# replace @imports	with actual file contents

		$importPattern = '/@import\s*'.$this->urlPattern.'\s*;?/i';
		preg_match_all($importPattern, $code, $matches);

		if(!empty($matches)) {

			foreach($matches[0] as $matchIndex=> $matchString) {

				$filepath = $matches[2][$matchIndex];
				if(file_exists($filepath)) {
					$contents = file_get_contents($filepath);

					$contents = $this->fixRelativeUrls($filepath, $contents);
					$code = str_replace($matchString, $contents, $code);
				} else {
					throw new OrganizerException("@import: {$filepath} not found");
				}
			}
		}

		return $code;
	}

	protected function preMergeProcessCode($path, $code) {
		// echo "$path => \n $code\n\n";
		$code = $this->fixRelativeUrls($path, $code);

		# remove all the @imports
		$code = $this->removeImports($path, $code);

		return $code;
	}
}


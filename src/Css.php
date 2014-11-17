<?php 

namespace Gckabir\Organizer;


class Css extends OrganizerObject implements IVariableContainer {

	protected $variables = array();
	protected $extension = '.css';
	
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
		$this->processVariables();
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

}


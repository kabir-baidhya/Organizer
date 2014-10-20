<?php 

namespace Gckabir\Organizer;


class Css extends OrganizerObject {

	protected $extension = '.css';
	


	public function __construct($bundle, array $styles, $version)
	{
		parent::__construct($bundle, $styles, $version);
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

}


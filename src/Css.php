<?php 

namespace Gckabir\Organizer;


use Gckabir\Organizer\AwesomeCache\CacheData;

class Css extends OrganizerObject {

	protected $extension = '.css';
	protected $version = '1.0';


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


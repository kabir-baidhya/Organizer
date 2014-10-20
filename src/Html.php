<?php 
namespace Gckabir\Organizer;


class Html extends OrganizerObject {
	protected $extension = '.html';

	public function __construct($bundle, array $templates, $version)
	{
		parent::__construct($bundle, $templates, $version);
	}


	protected function signature() {
		return OZR::getConfig('signature')
		? (
			'<!-- '."\n".
			' ! '.$this->bundle.' v'.$this->version.' | '.gmdate("M d Y H:i:s")." UTC\n".
			' ! Organized by Organizer'."\n".
			' ! https://github.com/kabir-baidhya/organizer'."\n".
			' !-->'."\n"

		) : null;
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
		$this->embedHere();
	}

	public function embedHere() {
		echo $this->preEmbedContent();
	}

}
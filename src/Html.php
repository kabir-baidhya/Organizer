<?php
namespace Gckabir\Organizer;

class Html extends OrganizerObject
{
    protected $extension = '.html';

    protected $commentDelimeter1 = '<!--';
    protected $commentDelimeter2 = '!-->';
    protected $commentFormatter = '!';

    public function __construct($bundle, array $templates, $version)
    {
        parent::__construct($bundle, $templates, $version);
    }

    public function output()
    {
        return $this->signature().$this->output;
    }

    public function outputMinified()
    {
        return $this->signature().\CssMin::minify($this->output());
    }

    public function includeHere()
    {
        $this->embedHere();
    }

    public function embedHere()
    {
        echo $this->preEmbedContent();
    }
}

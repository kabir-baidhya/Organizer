<?php
/*
 * This file is part of the Organizer package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\Organizer;

use Gckabir\Organizer\AwesomeCache\CacheData;
use Gckabir\Organizer\Misc\Helper;

abstract class OrganizerObject
{

    protected $bundle = null;
    protected $includes = array();
    protected $output = null;
    protected $version = null;

    protected $commentDelimeter1 = '/*';
    protected $commentDelimeter2 = '*/';
    protected $commentFormatter = '*';

    public function __construct($bundle, array $includes, $version)
    {
        $this->bundle = $bundle;
        $this->includes = $includes;
        $this->version = $version;

        $objectType = $this->getType();
        $this->config = OZR::getConfig($objectType);
    }

    protected function getType()
    {
        $objectType = strtolower(basename(get_called_class()));
        $objectType = explode('\\', $objectType);
        $objectType = array_pop($objectType);

        return $objectType;
    }

    /**
     * include a new file
     */
    public function add($item)
    {
        if (is_array($item)) {
            $this->includes = array_merge($this->includes, $item);
        } elseif (is_string($item) and !in_array($item, $this->includes)) {
            $this->includes[] = $item;
        }

        return $this;
    }

    /**
     * include a file before all other includes
     */
    public function addBefore($item)
    {
        if (is_array($item)) {
            $this->includes = array_merge($item, $this->includes);
        } elseif (is_string($item) and !in_array($item, $this->includes)) {
            array_unshift($this->includes, $item);
        }

        return $this;
    }

    /**
     * Add code directly
     */
    public function addCode($string)
    {
        if (!is_string($string)) {
            throw new OrganizerException("Invalid code");
        }

        $code = (object) array(
            'type'        => 'embeded',
            'code'        => $string,
            );
        $this->includes[] = $code;
    }

    protected function signature()
    {
        $a = $this->commentDelimeter1.' ';
        $b = ' '.$this->commentFormatter.' ';
        $c = ' '.$this->commentDelimeter2;

        return OZR::getConfig('signature')
        ? (
            $a."\n".
            $b.$this->bundle.' v'.$this->version.' | '.gmdate("M d Y H:i:s")." UTC\n".
            $b.'Organized by Organizer'."\n".
            $b.'https://github.com/kabir-baidhya/organizer'."\n".
            $c."\n"
            ) : null;
    }

    public function merge()
    {
        $mergedCode = '';
        foreach ($this->includes as $singleItem) {
            if (is_string($singleItem)) {
                $code = $this->getSourceCode($singleItem);
            } elseif (isset($singleItem->type) && $singleItem->type == 'embeded') {
                $code = $singleItem->code;
                $path = $this->config['basePath'];
            } else {
                throw new OrganizerException("Invalid code");
            }

            $code = $this->preMergeProcessCode(@$path, $code);

            $mergedCode .= "\n".$code;
        }

        $this->output = $mergedCode;

        return $this;
    }

    /**
     * Get source code from a file or merged-code from files matched by pattern
     */
    protected function getSourceCode($fileOrPattern)
    {
        $path = $this->config['basePath'].$fileOrPattern.$this->extension;

        $code  = '';
        if (file_exists($path)) {
            // if its a file get its code
            $code = file_get_contents($path);
        } elseif (Helper::hasWildcards($fileOrPattern)) {

            // if its pattern, get the merged code of all the files matched
            $matches = $this->filesByPattern($fileOrPattern);
            if (!empty($matches)) {
                foreach ($matches as $filePath) {
                    $code .= "\n".file_get_contents($filePath);
                }
            }
        } else {
            throw new OrganizerException($path." not found");
        }

        return $code;
    }

    /**
     * Returns the list of relevent files(with corresponding extension)
     * matched by the given pattern 
     * @param string $patternText
     * @return array
     */
    protected function filesByPattern($patternText)
    {
        # check if any kind of pattern is provided
        $pattern = $this->config['basePath'].$patternText;

        if (Helper::endsWith($patternText, '*')) {
            $pattern    .= $this->extension;
        } elseif (!Helper::endsWith($patternText, $this->extension)) {
            $pattern    .= '*'.$this->extension;
        }

        $matches = glob($pattern);

        return $matches;
    }

    public function build()
    {
        $uniqueString = $this->uniqueBundleString();

        $item = new CacheData($uniqueString);
        $isCachingEnabled = $this->config['cache'];

        if (!$isCachingEnabled or !$item->isCachedAndUsable()) {
            $this->merge();
            $content = $this->config['minify'] ? $this->outputMinified() : $this->output();
            $item->putInCache($content);
        }

        return $this->url();
    }

    public function url()
    {
        $serverUrl = OZR::getServerUrl();
        $parameter = $this->config['parameter'];

        $url = $serverUrl.'?'.http_build_query(array(
            $parameter        => $this->uniqueBundleString(),
            'ver'            => $this->version,
            ));

        return $url;
    }

    protected function uniqueBundleString()
    {
        return base64_encode($this->getType().'-'.$this->bundle);
    }

    protected function preEmbedContent()
    {
        $cacheEnabled = $this->config['cache'];

        $uniqueString = $this->uniqueBundleString();

        $data = new CacheData($uniqueString);

        if ($cacheEnabled and $data->isCachedAndUsable()) {
            $content = $data->cachedData();
        } else {
            $this->build();
            $content = $this->config['minify'] ? $this->outputMinified() : $this->output();
        }

        return $content;
    }

    protected function preMergeProcessCode($path, $code)
    {
        return $code;
    }

    abstract public function includeHere();
    abstract public function embedHere();
    abstract public function output();
    abstract public function outputMinified();
}

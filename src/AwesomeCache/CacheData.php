<?php

namespace Gckabir\Organizer\AwesomeCache;

use Gckabir\Organizer\OrganizerException;

class CacheData
{

    private static $config = array(
        'directory'    =>  'cache/',
        'cacheExpiry'    => 86400,//24 hour
        'serialize'        => true,
        );

    private $key = null;
    private $file = null;

    public function __construct($key)
    {
        $this->key = $key;
        $directory = static::$config['directory'];
        $this->file = $directory.$this->key;

        if (!file_exists($directory) && !is_dir($directory)) {
            mkdir($directory);
        }
    }

    public function cachedData()
    {
        if (!$this->isCached()) {
            return false;
        }

        $contents = file_get_contents($this->file);

        $serializationEnabled = static::$config['serialize'];

        $data = $serializationEnabled ? unserialize($contents) : $contents;

        return $data;
    }

    public function putInCache($data)
    {
        if (!$data) {
            return false;
        }

        $serializationEnabled = static::$config['serialize'];

        $data = $serializationEnabled ? serialize($data) : $data;

        $written = @file_put_contents($this->file, $data);

        if (!$written) {
            throw new OrganizerException("Couldn't write to file: {$this->file}");
        }
    }

    public function isCached()
    {
        return file_exists($this->file) && is_file($this->file);
    }

    public function isUsable()
    {
        return ($this->duration() < static::$config['cacheExpiry']);
    }

    public function duration()
    {
        if (!$this->isCached()) {
            return 0;
        }

        $duration = time() - $this->lastModified();

        return $duration;
    }

    public function lastModified()
    {
        clearstatcache();

        return filemtime($this->file);
    }

    public function isCachedAndUsable()
    {
        return ($this->isCached() and $this->isUsable());
    }

    public static function config($config)
    {
        static::$config = $config + static::$config;
    }

    public static function clearAll()
    {
        $dir = static::$config['directory'];

        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if (!is_dir($file)) {
                @unlink($dir.$file);
            }
        }
        closedir($dh);
    }

    public static function countAll()
    {
        $count = 0;
        $dir = static::$config['directory'];

        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if (!is_dir($file)) {
                $count++;
            }
        }
        closedir($dh);

        return $count;
    }

    public static function clear($key)
    {
        $that = new static($key);

        if ($that->isCached()) {
            @unlink($that->file);
        }
    }
}

<?php

namespace Gckabir\Organizer;

use Gckabir\Organizer\AwesomeCache\CacheData;

class OZR
{

    private static $config = array(
        'baseUrl'    => '',
        'serverUri'    => '',
        'cacheDir'    => '_organizer-cache/',
        'cacheExpiration'    => 1296000, //15-days
        'automaticServe'    => true,
        'signature'            => true,
        'css'    => array(
            'basePath'            => 'css/',
            'minify'            => false,
            'cache'                => false,
            'parameter'    => '_OZRcssSX',
            'browserCacheMaxAge' => 864000, //10days
            ),
        'javascript'    => array(

            'useStrict' => false,
            'basePath'    => 'scripts/',
            'wrap'        => false,
            'dependencies' => array(),
            'minify'    => false,
            'cache'        => false,
            'parameter'    => '_OZRjsSX',
            'browserCacheMaxAge' => 864000, //10days
            'appendVariables'    => false,
            ),
        'html'    => array(
            'parameter'    => '_OZRhtmlSX',
            'basePath'    => 'templates/',
            'minify'    => false,
            'cache'        => false,
            'browserCacheMaxAge' => 864000, //10days
            ),
        );

    private static $organizedJs;
    private static $organizedCss;
    private static $initialized = false;

    /* Static Methods */
    public static function init(array $config = array())
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            static::$config['baseUrl'] = $_SERVER['REQUEST_URI'];//default
        }

        # Overriding config
        foreach ($config as $key => $value) {
            if (!is_array($value)) {
                static::$config[$key] = $value;
            } else {
                foreach ($value as $var => $varValue) {
                    static::$config[$key][$var] = $varValue;
                }
            }
        }

        # Initialize Cache

        $cacheConfig = array(
            'directory'    => static::$config['cacheDir'],
            'cacheExpiry'    => static::$config['cacheExpiration'],
            'serialize'        => false,
            );

        CacheData::config($cacheConfig);

        static::$initialized = true;

        if (static::$config['automaticServe']) {
            static::serve();
        }
    }

    public static function getConfig($var = null)
    {
        static::checkInitialized();

        if ($var) {
            return static::$config[$var];
        } else {
            return static::$config;
        }
    }

    public static function organizeJS($bundle, array $scripts = array(), $version = '1.0')
    {
        static::checkInitialized();

        return new Javascript($bundle, $scripts, $version);
    }

    public static function organizeCSS($bundle, array $styles = array(), $version = '1.0')
    {
        static::checkInitialized();

        return new Css($bundle, $styles, $version);
    }

    public static function organizeHTML($bundle, array $templates = array(), $version = '1.0')
    {
        static::checkInitialized();

        return new Html($bundle, $templates, $version);
    }

    public static function serve()
    {
        static::checkInitialized();
        $jsQuery = static::$config['javascript']['parameter'];
        $cssQuery = static::$config['css']['parameter'];
        $htmlQuery = static::$config['html']['parameter'];

        if (isset($_GET[$jsQuery])) {
            $config = static::getConfig('javascript');
            $bundle = $_GET[$jsQuery];
            $contentType = 'text/javascript';
        } elseif (isset($_GET[$cssQuery])) {
            $config = static::getConfig('css');
            $bundle = $_GET[$cssQuery];
            $contentType = 'text/css';
        } elseif (isset($_GET[$htmlQuery])) {
            $config = static::getConfig('html');
            $bundle = $_GET[$htmlQuery];
            $contentType = 'text/html';
        } else {
            throw new OrganizerException("Resource not recognized");
        }

        static::serveBundle($bundle);
    }

    private static function serveBundle($bundle, $contentType, $config)
    {
        $content = new CacheData($bundle);

        if ($content->isCachedAndUsable()) {
            header('Content-Type: '.$contentType);

            # Cache Control headers

            if (!$config['browserCacheMaxAge']) {
                $config['browserCacheMaxAge'] = 0;
            }

            $expires = $config['browserCacheMaxAge'];
            $lastModifiedDate = gmdate("D, d M Y H:i:s", $content->lastModified())." GMT";
            $etag = md5($bundle);
            header('Cache-Control: private, max-age='.$expires.', pre-check='.$expires);
            header("Pragma: private");
            header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expires).' GMT');
            header("Last-Modified: ".$lastModifiedDate);

            $condition1 = (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModifiedDate);

            if ($condition1) {
                header("HTTP/1.1 304 Not Modified");
                exit;
            }

            # Echo file content
            echo $content->cachedData();
        } else {
            # invalid query | cache expired | file not found
            header('HTTP/1.1 404 Not Found');
            echo "<h1>404 Not Found</h1>";
        }
        die();
    }

    private static function checkInitialized()
    {
        if (!static::$initialized) {
            throw new OrganizerException("Organizer not initialized");
        }
    }

    public static function clearCache()
    {
        static::checkInitialized();
        CacheData::clearAll();
    }

    public static function countCachedFiles()
    {
        return CacheData::countAll();
    }

    public static function getBaseUrl()
    {
        $baseUrl = rtrim(static::getConfig('baseUrl'), '/').'/';

        return $baseUrl;
    }

    public static function getServerUrl()
    {
        $baseUrl = static::getBaseUrl();
        $serverUri = ltrim(static::getConfig('serverUri'), '/');

        return $baseUrl.$serverUri;
    }
}

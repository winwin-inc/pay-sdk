<?php

namespace winwin\pay\sdk\support;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;

final class Util
{
    /**
     * Access a value in an array, returning a default value if not found
     *
     * Will also do a case-insensitive search if a case sensitive search fails.
     *
     * @param array $values
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $values, $key, $default = null)
    {
        if (array_key_exists($key, $values)) {
            return $values[$key];
        }

        return $default;
    }

    /**
     * Make snake-case strings.
     *
     * <code>
     *    echo Util::snakeCase('CocoBongo'); // coco_bongo
     *    echo Util::snakeCase('CocoBongo', '-'); // coco-bongo
     * </code>
     */
    public static function snakeCase($str, $delimiter = null)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode($delimiter === null ? '_' : $delimiter, $ret);
    }

    /**
     * Get current server ip.
     *
     * @return string
     */
    public static function getServerIp()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        } else {
            // for php-cli(phpunit etc.)
            $ip = gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    public static function generateSign(array $data, $secret, $method = 'md5')
    {
        $data = array_filter($data, function ($val) {
            return $val === null || $val !== '';
        });
        ksort($data);
        $pairs = [];
        foreach ($data as $name => $val) {
            $pairs[] = $name.'='.$val;
        }
        $pairs[] = 'key='.$secret;

        return strtoupper(call_user_func($method, implode('&', $pairs)));
    }

    /**
     * Create Psr server request
     *
     * Borrow from Zend\Diactoros\ServerRequestFactory
     * 
     * @return ServerRequestInterface
     */
    public static function createRequestFromGlobals()
    {
        $server = $_SERVER;
        $headers = self::marshalHeaders($server);
        
        return new ServerRequest(
            self::get($server, 'REQUEST_METHOD', "GET"),
            $uri = self::marshalUriFromServer($server, $headers),
            $headers,
            $body = 'php://input',
            $version = '1.1',
            $server
        );
    }

    private static function marshalHeaders(array $server)
    {
        $headers = [];
        foreach ($server as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);

                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }

            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(strtolower(substr($key, 5)), '_', '-');
                $headers[$name] = $value;
                continue;
            }

            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = 'content-' . strtolower(substr($key, 8));
                $headers[$name] = $value;
                continue;
            }
        }

        return $headers;
    }

    private static function marshalUriFromServer(array $server, array $headers)
    {
        $uri = new Uri('');

        // URI scheme
        $scheme = 'http';
        $https  = self::get($server, 'HTTPS');
        if ($https && 'off' !== $https) {
            $scheme = 'https';
        }
        $uri = $uri->withScheme($scheme);

        // Set the host
        if (isset($headers['host'])) {
            $host = explode(':', $headers['host'], 2);
            $uri = $uri->withHost($host[0]);
            if (isset($host[1]) && is_numeric($host[1])) {
                $uri = $uri->withPort((int) $host[1]);
            }
        } elseif (isset($server['SERVER_NAME'])) {
            $uri = $uri->withHost($server['SERVER_NAME']);
            if (isset($server['SERVER_PORT'])) {
                $uri = $uri->withPort((int) $server['SERVER_PORT']);
            }
        }

        // URI path
        $path = self::marshalRequestUri($server);
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }

        // URI query
        $query = '';
        if (isset($server['QUERY_STRING'])) {
            $query = ltrim($server['QUERY_STRING'], '?');
        }

        // URI fragment
        $fragment = '';
        if (strpos($path, '#') !== false) {
            list($path, $fragment) = explode('#', $path, 2);
        }

        return $uri
            ->withPath($path)
            ->withFragment($fragment)
            ->withQuery($query);
    }

    private static function marshalRequestUri(array $server)
    {
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = self::get($server, 'IIS_WasUrlRewritten');
        $unencodedUrl    = self::get($server, 'UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }

        $requestUri = self::get($server, 'REQUEST_URI');

        // Check this first so IIS will catch.
        $httpXRewriteUrl = self::get($server, 'HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = self::get($server, 'HTTP_X_ORIGINAL_URL');
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }

        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        $origPathInfo = self::get($server, 'ORIG_PATH_INFO');
        if (empty($origPathInfo)) {
            return '/';
        }

        return $origPathInfo;
    }
}

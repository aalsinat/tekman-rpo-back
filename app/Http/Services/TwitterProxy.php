<?php
/**
 * Proy class for Twitter REST API
 * User: alexa
 * Date: 18/04/2018
 * Time: 21:50
 */

namespace App\Http\Services;


use Illuminate\Support\Facades\Log;

class TwitterProxy
{
    private $config = [
        'base_url' => 'https://api.twitter.com/1.1/'
    ];

    /**
     * @param    string $oauth_access_token OAuth Access Token
     * @param    string $oauth_access_token_secret OAuth Access Token Secret
     * @param    string $consumer_key Consumer key
     * @param    string $consumer_secret Consumer secret
     * @param    string $user_id User id
     */
    public function __construct($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret, $user_id)
    {
        $this->config = array_merge($this->config, compact('oauth_access_token', 'oauth_access_token_secret', 'consumer_key', 'consumer_secret', 'user_id', 'screen_name', 'count'));
    }

    public function get($url)
    {
        Log::info('Calling get last tweets:' . $url);
        if (!isset($url)) {
            die('No URL set');
        }
        // Figure out the URL parameters
        $url_parts = parse_url($url);
        parse_str($url_parts['query'], $url_arguments);

        $full_url = $this->config['base_url'] . $url; // URL with the query on it
        $base_url = $this->config['base_url'] . $url_parts['path']; // URL without the query

        Log::info('Full url: ' . $full_url);
        Log::info('Base url. ' . $base_url);

        // Set up the OAuth Authorization array
        $oauth = [
            'oauth_consumer_key' => $this->config['consumer_key'],
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->config['oauth_access_token'],
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        ];

        $base_info = $this->buildBaseString($base_url, 'GET', array_merge($oauth, $url_arguments));

        $composite_key = rawurlencode($this->config['consumer_secret']) . '&' . rawurlencode($this->config['oauth_access_token_secret']);

        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));

        // Make Requests
        $header = [
            $this->buildAuthorizationHeader($oauth),
            'Expect:'
        ];
        $options = [
            CURLOPT_HTTPHEADER => $header,
            //CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $result = curl_exec($feed);
        $info = curl_getinfo($feed);
        curl_close($feed);

        // Send suitable headers to the end user.
        if (isset($info['content_type']) && isset($info['size_download'])) {
            header('Content-Type: ' . $info['content_type']);
            header('Content-Length: ' . $info['size_download']);
        }

        return $result;
    }

    private function buildBaseString($baseURI, $method, $params)
    {
        $r = [];
        ksort($params);
        foreach ($params as $key => $value) {
            $r[] = "$key=" . rawurlencode($value);
        }

        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    private function buildAuthorizationHeader($oauth)
    {
        $r = 'Authorization: OAuth ';
        $values = [];
        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $r .= implode(', ', $values);

        return $r;
    }
}
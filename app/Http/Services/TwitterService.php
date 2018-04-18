<?php
/**
 * Twitter proxy service.
 * User: Alex Alsina
 * Date: 18/04/2018
 * Time: 21:50
 */

namespace App\Http\Services;


use Illuminate\Support\Facades\Log;

class TwitterService
{
    /**
     * Twitter proxy object used to interact with twitter api.
     */
    private $_proxy;

    /**
     * Initialize proxy object that will be used to interact with Twitter API
     * @param \App\Http\Services\TwitterProxy $proxy
     */
    public function __construct($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret, $user_id)
    {
        $this->_proxy = new TwitterProxy($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret, $user_id);
    }

    public function setProxy($proxy) {
        $this->_proxy = $proxy;
        Log::info('$this->_proxy object:' . json_encode($this->_proxy));
    }
    /**
     * Returns a collection of the most recent Tweets posted by the user indicated by the screen_name.
     * @param string $screen_name The screen name of the user for whom to return results.
     * @param int $count Specifies the number of Tweets to try and retrieve, up to a maximum of 200 per distinct request.
     * @return string Collection of Tweets
     */
    public function getLastTweets($screen_name, $count = 10)
    {
        $url = 'statuses/user_timeline.json';
        $url .= '?screen_name=' . $screen_name;
        $url .= '&count=' . $count;
        return $this->_proxy->get($url);
    }
}
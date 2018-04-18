<?php
/**
 * Lumen service provider for Twitter service.
 * User: Alex Alsina
 * Date: 18/04/2018
 * Time: 10:00
 */

namespace App\Providers;

use App\Http\Services\TwitterService;
use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    private $_oauth_access_token;
    private $_oauth_access_token_secret;
    private $_consumer_key;
    private $_consumer_secret;
    private $_user_id;

    /**
     * TwitterServiceProvider constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_oauth_access_token = env('TWITTER_API_OAUTH_ACCESS_TOKEN');
        $this->_oauth_access_token_secret = env('TWITTER_API_OAUTH_ACCESS_TOKEN_SECRET');
        $this->_consumer_key = env('TWITTER_API_CONSUMER_KEY');
        $this->_consumer_secret = env('TWITTER_API_CONSUMER_SECRET');
        $this->_user_id = env('TWITTER_API_USER_ID');
    }

    /**
     * Register bindings in the container
     */
    public function register()
    {
        $this->app->singleton(TwitterService::class, function ($app) {
            return new TwitterService($this->_oauth_access_token, $this->_oauth_access_token_secret, $this->_consumer_key, $this->_consumer_secret, $this->_user_id);
        });
    }
}
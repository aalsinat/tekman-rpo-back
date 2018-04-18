<?php
/**
 * Controller for unique endpont
 * User: Alex Alsina
 * Date: 18/04/2018
 * Time: 21:50
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\TwitterService;
use Illuminate\Support\Facades\Cache;

class TwitterController extends Controller
{
    private $_service;

    /**
     * Create a new Twitter controller instance for serving api routes.
     *
     * @param TwitterService $service Injected Twitter service
     */
    public function __construct(TwitterService $service)
    {
        $this->_service = $service;
    }

    /**
     * Returns a collection of the most recent Tweets posted by the user indicated by the id.
     * @param string $id The screen name of the user for whom to return results.
     * @param Request $request Http request object
     * @return string Collection of Tweets
     */
    public function getLastTweets($id, Request $request) {
        if (Cache::has($id)) {
            return Cache::get($id);
        }
        $lastTweets = $this->_service->getLastTweets($id, 10);
        Cache::add($id, $lastTweets, 30);
        return $lastTweets;
    }
}
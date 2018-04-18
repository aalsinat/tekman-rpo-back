<?php
/**
 * Tests for Twitter API Proxy
 * User: Alex Alsina
 * Date: 19/04/2018
 * Time: 0:36
 */

class TwitterProxyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTotalAmountOfTweets()
    {
        $this->get('/api/user/marcvidal/last-tweets');

        $this->assertNotNull($this->response->getContent());
        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
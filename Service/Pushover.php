<?php

namespace S40\PushoverBundle\Service;

use Guzzle\Http\Client;
use S40\PushoverBundle\Exception\PushoverException;

/**
 * Pushover
 */
class Pushover
{
    /**
     * Messages api url
     * 
     * @var string
     */
    const MESSAGES_URL  = 'https://api.pushover.net/1/messages.json';
    
    /**
     * Pushover token
     * 
     * @var string
     */
    private $token;

    /**
     * Pushover target user
     *
     * @var string
     */
    private $user;
    
    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
        $this->client = new Client();
    }
    
    private function buildParams(array $params)
    {
        return array_merge(
    	    $params,
            [
    	       'token'  => $this->token,
               'user'   => $this->user
            ]
        );
    }
    
    private function send($url, $params)
    { 
        $request = $this->client->post(
            $url,
            [],
            http_build_query($this->buildParams($params))
        );
        
        $response = $request->send();
        
        if($response->getStatusCode() != 200) {
            throw new PushoverException('Invalid server response', $response->getStatusCode());
        }
        
        return $response->getBody();
    }
    
    public function push($title, $message, array $params = [])
    {
        $params['title']    = $title;
        $params['message']  = $message;
        
        return $this->send(self::MESSAGES_URL, $params);
    }
}
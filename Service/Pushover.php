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
    
    /**
     * Constructor
     * 
     * @param string $token
     * @param string $user
     */
    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
        $this->client = new Client();
    }
    
    /**
     * Build post data params
     * 
     * @param array $params
     * @return array
     */
    private function buildParams(array $params)
    {
        return http_build_query(array_merge(
    	    $params,
            [
    	       'token'  => $this->token,
               'user'   => $this->user
            ]
        ));
    }
    
    /**
     * Send a request
     * 
     * @param string $url
     * @param array $params
     * @return string
     * @throws PushoverException
     */
    private function send($url, array $params)
    { 
        $request = $this->client->post(
            $url,
            [],
            $this->buildParams($params)
        );
        
        $response = $request->send();
        
        if($response->getStatusCode() != 200) {
            throw new PushoverException('Invalid server response', $response->getStatusCode());
        }
        
        return $response->getBody();
    }
    
    /**
     * Push a message
     * 
     * @param string $title
     * @param string $message
     * @param array $params
     * @return string
     */
    public function push($title, $message, array $params = [])
    {
        $params['title']    = $title;
        $params['message']  = $message;
        
        return $this->send(self::MESSAGES_URL, $params);
    }
}
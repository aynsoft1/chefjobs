<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
class GoogleOAuth2 
{
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
  public $host = "https://www.googleapis.com/plus/v1/";
  /* Set timeout default. */
  public $timeout = 30;
  /* Set connect timeout. */
  public $connecttimeout = 30; 
  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  /* Respons format. */
  public $format = 'json';
  /* Decode returned json data. */
  public $decode_json = TRUE;
  /* Contains the last HTTP headers returned. */
  public $http_info;
  /* Set the useragnet. */
  public $useragent = 'GoogleOAuth 2 beta';
  /* Set the state. */
  public $state;
  /* Set the api key. */
  public $clientId;
  /* Set the api Secret. */
  public $clientSecret;
  /* Set the api call back url. */
  public $redirectUri;
  /* Set the api accessToken. */
  public $accessToken;

  const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';
  const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
  const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';

  function __construct($clientId, $clientSecret,$redirectUri)
  {
   $this->clientId  =$clientId;
   $this->clientSecret  =$clientSecret;
   $this->redirectUri  =$redirectUri;
  }
  public function post($url, $parameters = array()) {
     $response = $this->http($url, 'POST', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response,true);
    }
    return $response;
  }
  public function createAuthUrl($scope) {
    $params = array(
        'response_type=code',
        'redirect_uri=' . urlencode($this->redirectUri),
        'client_id=' . urlencode($this->clientId),
        'scope=' .urlencode($scope),
        'approval_prompt=force',
     );
    if (isset($this->state)) {
      $params[] = 'state=' . urlencode($this->state);
    }
    $params = implode('&', $params);
    return self::OAUTH2_AUTH_URL . "?$params";
  }

  public function setState($state) {
    $this->state = $state;
  }
    public function getAccessToken() {
    return json_encode($this->accessToken);
  }

  /**
   * @param $accessToken
   * @throws apiAuthException Thrown when $accessToken is invalid.
   */
  public function setAccessToken($accessToken) {

     $accessToken = json_decode($accessToken, true);
     if ($accessToken == null) {
      die('Could not json decode the access token');
    }
    if (! isset($accessToken['access_token'])) {
      die("Invalid token format");
    }
    $this->accessToken = $accessToken;
  }
  public function authenticate($code)
  {
   if ($code!='')
   {
     $parameter=array(
          'code' => $code,
          'grant_type' => 'authorization_code',
          'redirect_uri' => $this->redirectUri,
          'client_id' => $this->clientId,
         'client_secret' => $this->clientSecret);
     $content = $this->post(self::OAUTH2_TOKEN_URI,$parameter);
     if($this->http_code==200)
     {
      $content=json_encode($content);
      $this->setAccessToken($content);
      return $this->getAccessToken();
     }
     else
     {
      die("Error fetching OAuth2 access token");
     }      
   }
  }
  
  public function get($url, $parameters = array()) {
    $response = $this->http($url, 'GET', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response,true);
    }
    return $response;
  }
  
  public function http($url, $method, $postfields = NULL) {
    $this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);

    $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    $this->url = $url;
    curl_close ($ci);
    return $response;
  }
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value;
    }
    return strlen($header);
  }
}
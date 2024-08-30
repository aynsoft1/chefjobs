<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2019  #**********
***********************************************************
*/
class LinkedIn 
{
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
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
  public $useragent = 'LinkedinOAuth  beta';
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
  public $errorMessage;

  const API_BASE_URI = 'https://api.linkedin.com/v2';
  const OAUTH2_TOKEN_URI = 'https://www.linkedin.com/oauth/v2/accessToken';
  const OAUTH2_AUTH_URL  = 'https://www.linkedin.com/oauth/v2/authorization';

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
        'client_id=' . urlencode($this->clientId),
        'redirect_uri=' . urlencode($this->redirectUri),
        'scope=' .urlencode($scope),
     );
    if (isset($this->state)) {
      $params[] = 'state=' . urlencode($this->state);
    }
    $params = implode('&', $params);
    return self::OAUTH2_AUTH_URL . "?$params";
  }
  public function checkAppKey(){
    $parameter = array(
        'grant_type=client_credentials',
        'client_id=' . urlencode($this->clientId),        
        'client_secret='.urlencode($this->clientSecret),
     );
    $parameter = implode('&', $parameter);
    $content = $this->http(self::OAUTH2_TOKEN_URI, 'POST', $parameter);
    if($this->http_code!=200)
    {
	 $content = json_decode($content,true);
     $error_message = tep_db_prepare_input($content['error_description']);
     if(preg_match('/Error validating client secret./i',$error_message))
     $this->errorMessage='Incorrect Application signature';
     elseif(preg_match('/Error validating application./i',$error_message))
     $this->errorMessage='Incorrect Application ID';
     else
     $this->errorMessage=$content['error_description'];
     return false;
    }
    else
     return true;
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
          'code='.urlencode($code),
		  'grant_type='.urlencode('authorization_code'),
          'redirect_uri='.urlencode($this->redirectUri),
          'client_id='.urlencode($this->clientId),
         'client_secret='.urlencode($this->clientSecret));
      $parameter = implode('&', $parameter);
	  $response = $this->http(self::OAUTH2_TOKEN_URI, 'POST', $parameter);
 
     
     $params = null; 
     //parse_str($response, $params);
	 $params=json_decode($response, true);
     if($params['access_token'])
     {
      $content=json_encode($params);
      $this->setAccessToken($content);
      return $this->getAccessToken();
     }
     else
     {
      die("Error fetching OAuth2 access token");
     }      
   }
  }
  public function getProfile() {
	  $access_tokenc= $this->accessToken['access_token'];
      $profile_url = self::API_BASE_URI .'/me/?oauth2_access_token='.$access_tokenc;
      $content = $this->get($profile_url);	  
      return $content;
  }
 public function getProfileEmail() {
	  $access_tokenc= $this->accessToken['access_token'];
      $profile_url = self::API_BASE_URI .'/clientAwareMemberHandles?q=members&projection=(elements*(primary,type,handle~))&oauth2_access_token='.$access_tokenc;
      $content = $this->get($profile_url);	 
	  if(isset ($content['elements'][0]['handle~']['emailAddress']))
	   return $content['elements'][0]['handle~']['emailAddress'];
	  else
       return false;
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
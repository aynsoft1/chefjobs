<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
class Facebook
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
  public $useragent = 'Facebook beta';
  /* Set the access_type. */

  const FACEBOOK_GRAPH_URI = 'https://graph.facebook.com';

  function __construct($url =null)
  {
   if ($url != null)
   {
    $this->url = $url;
   }
  }
  public function setUrl($url)   
  {
   $this->url = $url;
  }
  public function getFacebookInfo($url='',$access_token='') 
  {
   if($url=='')	 
   $url = $this ->url;
   if($url=='')	 
   return false;  
   $pageId= '';
   if(preg_match('/(?:https:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\.\-]*)/',$url,$match))
   {
    $pageId = $match[1];
   }
   elseif(preg_match('/^\d+/',$url,$match))
   {
    $pageId = $match[0];
   }
   elseif(preg_match('/^\b\w+\b/',$url,$match))
   {
    $pageId = $match[0];
   }
			//echo $pageId;die('ok');
   if($pageId=='')
    return false;  
    $content = $this->get(self::FACEBOOK_GRAPH_URI.'/v16.0/'.urlencode($pageId).'?access_token='.$access_token);
    if($this->http_code==200)
	{
     return($content);
    }
    return false;
  }

  public function post($url, $parameters = array()) {
     $response = $this->http($url, 'POST', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response,true);
    }
    return $response;
  }
   
  public function get($url, $parameters = array()) {
    $str='';
    if(is_array($parameters))
   	{
	 foreach( $parameters as $key => $value) 
	 {
	  $str.=trim($key).'='.urlencode(trim($value)).'&';
	 }
	}
	if($str!='')
    {
	 if(preg_match('/\?/i',$url,$match))
	 $url=$url.'&'.$str;
	 else
	 $url=$url.'?'.$str;
	}
    $response = $this->http($url, 'GET');
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
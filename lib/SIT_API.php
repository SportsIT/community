<?php

class SIT_API{
  
  /**
   * Sign a URL with a given private key and return it with signature and optional publickey
   * 
   * Note that this URL must be properly URL-encoded
   * @link http://gmaps-samples.googlecode.com/svn/trunk/urlsigning/UrlSigner.php-source
   *
   * @param string $signedURL - url to sign
   * @param string $privateKey - private api key
   * @param string $publicKey (optional) - public key that gets added to query string of the signed url
   * @param string $encodedSignature - Encoded signature passed back by reference
   * @return string
   */
  public static function getSignedURL($signedURL, $privateKey, $publicKey, &$encodedSignature=null){
    
    // parse the url
    $url = parse_url($signedURL);
    $urlPartToSign = $url['path'];
    
    $queryString = array();
    if(isset($url['query'])){
      parse_str($url['query'], $queryString);
    }
         
    /* add on query part as it might be modified if there was a signature on it */
    if(!empty($queryString)){
      $urlPartToSign.="?".$url['query'];
    }
    
    /* if no query string add ? to prep for signature, etc*/
    else{
      $signedURL.="?";
      $urlPartToSign.="?";
    }
    
    /* check to see if it has expires on it, if not then add it automatically */
    if(!isset($queryString['Expires'])){
      $expiresString = "&Expires=".(time()+15*60);
      $signedURL.= $expiresString;
      $urlPartToSign .= $expiresString;
    }
    
  
    // Create a signature using the private key and the URL-encoded
    // string using HMAC SHA1. This signature will be binary.
    $signature = hash_hmac("sha1",$urlPartToSign, $privateKey,  true);
    $encodedSignature = self::encodeBase64UrlSafe($signature);
      
    // attach signature to url
    $signedURL.="&signature=".$encodedSignature;
    
    /* if specified, attached public key to url */
    if($publicKey){
      $signedURL.="&publickey=".$publicKey;    
    }
   // echo $signedURL;
    
    return $signedURL;
  }
  
  
  /**
   * Determines if an url is signed correctly and valid
   * 
   * @param string $urlString - fully qualified url to parse
   * @param string $privateKey - private key to check the signed url on
   * @return boolean
   */
  public static function isSignedURLValid($urlString, $privateKey){
  
    // parse the url
    $url = parse_url($urlString);
  
    $urlToSign = $url['path'];
    parse_str($url['query'], $queryVars);
  
    /* no signature found */
    if(empty($queryVars)){
      return false;
    }
    /* if url has signature, publickey then parse them out. They are not part of the signed url. */
    if(!empty($queryVars) && isset($queryVars['signature'])){
      /* http://stackoverflow.com/questions/1842681/regular-expression-to-remove-one-parameter-from-query-string */
      $url['query'] = preg_replace('/&signature(\=[^&]*)?(?=&|$)|^signature(\=[^&]*)?(&|$)/','',$url['query']);
      $url['query'] = preg_replace('/&publickey(\=[^&]*)?(?=&|$)|^publickey(\=[^&]*)?(&|$)/','',$url['query']);
      $url['query'] = preg_replace('/&oauth_token(\=[^&]*)?(?=&|$)|^oauth_token(\=[^&]*)?(&|$)/','',$url['query']);
      
      if(!empty($url['query'])){
        $urlToSign.='?'.$url['query'];
      }
    }
        
    /* get signature */
    $signature = null;
    self::getSignedURL($urlToSign, $privateKey, null, $signature);
  
    if($signature != $queryVars['signature']){  
      return false;
    }
    return true;
  
  }
  
  // Encode a string to URL-safe base64
  public static function encodeBase64UrlSafe($value){
    return str_replace(array('+', '/'), array('-', '_'),
    base64_encode($value));
  }
  
  // Decode a string from URL-safe base64
  public static function decodeBase64UrlSafe($value){
    return base64_decode(str_replace(array('-', '_'), array('+', '/'), $value));
  }
  
  public static function exchangeKey($oauth_token){
    $url = BASE_URL."/api/auth/token_exchange";
    $data = array('oauth_token' => $oauth_token, 'format' => 'json');
    
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

  }
}
?>

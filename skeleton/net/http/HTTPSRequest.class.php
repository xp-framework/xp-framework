<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

   uses ('net.http.HTTPRequest');
   
   /**
    * HTTPS requests
    *
    * @deprecated
    * @ext  curl
    */
   class HTTPSRequest extends HTTPRequest {
      var $url;
      var $offset= 0;
      
      function HTTPSRequest($params= NULL) {
         $this->__construct ($params);
      }   
      
      function __construct($params= NULL) {
         $this->port= 443;
         $this->offset= 0;
         if (isset ($params['url'])) {
            $this->url= $params['url'];
            $p= parse_url ($this->url);
            $params['host']= $p['host'];
            $params['port']= (isset ($p['port']) ? $p['port'] : 443);
            $params['target']= $p['path'].@$p['query'];
         }            
         HTTPRequest::__construct ($params);
      }
      
      function write($str) {
         //print $str;
         $curl= curl_init ($this->url);
         curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, $str);
         curl_setopt ($curl, CURLOPT_HEADER, 1);

         ob_start ();
         if (FALSE === ($ret= curl_exec ($curl))) {
           $this->tmp_response= FALSE;
           $ret= throw(new IOException(sprintf('%d: %s', curl_errno($curl), curl_error($curl))));
         } else {
           $this->tmp_response= explode ("\n", ob_get_contents ());
           $ret= strlen ($str);
         }
         curl_close ($curl);
         $this->tmp_response= explode ("\n", ob_get_contents ());
         ob_end_clean ();
         return $ret;
      }
      
      function read($size= NULL) {
         return $this->tmp_response[$this->offset++]."\n";
      }
      
      function eof() {
         return (count ($this->tmp_response) <= $this->offset);
      }
      
      function connect() {
         return true;
      }
      
      function close() {
         return true;
      }
   
   }

?>

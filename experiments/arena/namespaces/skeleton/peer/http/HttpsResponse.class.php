<?php
/* This class is part of the XP framework
 *
 * $Id: HttpsResponse.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace peer::http;

  ::uses('peer.http.HttpResponse');

  /**
   * Specialized subclass of HttpResonse for secure connections
   *
   * @ext      curl
   * @see      xp://peer.http.HttpResponse
   * @purpose  Response class for HTTPS
   */
  class HttpsResponse extends HttpResponse {
  
    /**
     * Read head if necessary
     *
     * @return  bool success
     */
    protected function _readhead() {
      if (0 != $this->statuscode) return TRUE;
      
      // Read status line
      $s= curl_getinfo($this->stream[0], CURLINFO_HEADER_SIZE);
      $h= explode("\r\n", substr($this->stream[1], 0, $s));
      $this->stream[1]= substr($this->stream[1], $s);

      if (3 != ($r= sscanf(
        $h[0], 
        'HTTP/%d.%d %3d', 
        $major, 
        $minor, 
        $this->statuscode
      ))) {
        throw(new lang::FormatException('"'.$h[0].'" is not a valid HTTP response ['.$r.']'));
      }

      $this->message= substr($s, 12);      
      $this->version= $major.'.'.$minor;
      
      // Read rest of headers
      for ($i= 1, $s= sizeof($h); $i < $s; $i++) {
        if (empty($h[$i])) continue;
        
        list($k, $v)= explode(': ', $h[$i], 2);
        $this->headers[$k]= $v;
      }
      
      return TRUE;
    }

    /**
     * Read data
     *
     * @param   int size default 8192
     * @param   bool binary default FALSE
     * @return  string buf or FALSE to indicate EOF
     */
    public function readData($size= 8192, $binary= FALSE) {
      if (!$this->_readhead()) return FALSE;            // Read head if not done before
      if (!isset($this->stream) || 0 == strlen($this->stream[1])) return FALSE;  // EOF
      
      $str= substr($this->stream[1], 0, $size);
      $size= strlen($str);
      if (!$binary) {
        if (FALSE === ($n= strpos($str, "\n"))) $n= $size;
        if (FALSE === ($r= strpos($str, "\r"))) $r= $size;
        $size= min($size, $n, $r);
        if ($r < $size) $size+= ("\n" == $str{$r+ 1});
        $str= substr($str, 0, $size+ 1);
        $size++;
      }
      
      if (FALSE === ($this->stream[1]= substr($this->stream[1], $size))) {
        curl_close($this->stream[0]);
        unset($this->stream);
      }
      return $str;
    }
    
  }
?>

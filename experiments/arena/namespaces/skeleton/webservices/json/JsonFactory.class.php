<?php
/* This class is part of the XP framework
 *
 * $Id: JsonFactory.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::json;

  uses('webservices.json.JsonDecoder');

  /**
   * Json decoder factory. Use this class to get instances
   * of decoders.
   *
   * This enables us to use bundled PHP extensions if they're
   * available or use the userland implementation as fallback.
   *
   * @see      http://json.org
   * @purpose  Factory
   */
  class JsonFactory extends lang::Object {
  
    /**
     * Create an instance of a decoder
     *
     * @return  webservices.json.IJsonDecoder
     */
    public static function create() {
      $n= new JsonDecoder();
      return $n;
    }
  }
?>

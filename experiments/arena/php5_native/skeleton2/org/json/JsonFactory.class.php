<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.json.JsonDecoder');

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
  class JsonFactory extends Object {
  
    /**
     * Create an instance of a decoder
     *
     * @model   static
     * @access  public
     * @return  &org.json.IJsonDecoder
     */
    public function &create() {
      $n= &new JsonDecoder();
      return $n;
    }
  }
?>

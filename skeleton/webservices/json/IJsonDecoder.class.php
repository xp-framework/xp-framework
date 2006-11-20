<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface a Json decoder has to implement
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class IJsonDecoder extends Interface {
  
    /**
     * Encode data into string
     *
     * @access  public
     * @param   mixed data
     * @return  string
     */
    function encode($data) { }
    
    /**
     * Decode string into data
     *
     * @access  public
     * @param   string string
     * @return  mixed
     */
    function decode($string) { }      
  
  }
?>

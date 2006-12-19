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
  interface IJsonDecoder {
  
    /**
     * Encode data into string
     *
     * @access  public
     * @param   mixed data
     * @return  string
     */
    public function encode($data);
    
    /**
     * Decode string into data
     *
     * @access  public
     * @param   string string
     * @return  mixed
     */
    public function decode($string);      
  
  }
?>

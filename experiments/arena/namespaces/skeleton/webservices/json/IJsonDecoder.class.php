<?php
/* This class is part of the XP framework
 *
 * $Id: IJsonDecoder.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace webservices::json;

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
     * @param   mixed data
     * @return  string
     */
    public function encode($data);
    
    /**
     * Decode string into data
     *
     * @param   string string
     * @return  mixed
     */
    public function decode($string);      
  
  }
?>

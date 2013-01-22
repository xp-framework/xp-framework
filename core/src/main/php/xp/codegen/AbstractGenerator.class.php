<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generator
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractGenerator extends Object {
    public 
      $storage = NULL,
      $output  = NULL;
   
    /**
     * Returns storage
     *
     * @return  xp.codegen.AbstractStorage
     */
    #[@target]
    public function storage() {
      return $this->storage;
    } 

    /**
     * Returns output
     *
     * @return  xp.codegen.AbstractStorage
     */
    #[@target]
    public function output() {
      return $this->output;
    } 
  }
?>

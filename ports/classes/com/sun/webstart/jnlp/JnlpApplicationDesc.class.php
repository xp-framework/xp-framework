<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents JNLP application description
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Wrapper class
   */
  class JnlpApplicationDesc extends Object {
    var
      $main_class = '',
      $arguments  = array();

    /**
     * Set Main_class
     *
     * @access  public
     * @param   string main_class
     */
    function setMain_class($main_class) {
      $this->main_class= $main_class;
    }

    /**
     * Get Main_class
     *
     * @access  public
     * @return  string
     */
    function getMain_class() {
      return $this->main_class;
    }
    
    /**
     * Add an arguments
     *
     * @access  public
     * @param   string argument
     */
    function addArgument($argument) {
      $this->arguments[]= $argument;
    }

    /**
     * Set Arguments
     *
     * @access  public
     * @param   string[] arguments
     */
    function setArguments($arguments) {
      $this->arguments= $arguments;
    }

    /**
     * Get Arguments
     *
     * @access  public
     * @return  string[]
     */
    function getArguments() {
      return $this->arguments;
    }
  }
?>

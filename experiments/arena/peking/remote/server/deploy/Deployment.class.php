<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Deployment
   *
   * @see      xp://remote.server.deploy.Deployable
   * @purpose  Deployment
   */
  class Deployment extends Object {
    var
      $origin           = '',
      $classloader      = NULL,
      $implementation   = '',
      $interface        = '',
      $directoryName    = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string origin
     */
    function __construct($origin) {
      $this->origin= $origin;
    }

    /**
     * Set Classloader
     *
     * @access  public
     * @param   &lang.Object classloader
     */
    function setClassloader(&$classloader) {
      $this->classloader= &$classloader;
    }

    /**
     * Get Classloader
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getClassloader() {
      return $this->classloader;
    }

    /**
     * Set Implementation
     *
     * @access  public
     * @param   string implementation
     */
    function setImplementation($implementation) {
      $this->implementation= $implementation;
    }

    /**
     * Get Implementation
     *
     * @access  public
     * @return  string
     */
    function getImplementation() {
      return $this->implementation;
    }

    /**
     * Set Interface
     *
     * @access  public
     * @param   string interface
     */
    function setInterface($interface) {
      $this->interface= $interface;
    }

    /**
     * Get Interface
     *
     * @access  public
     * @return  string
     */
    function getInterface() {
      return $this->interface;
    }

    /**
     * Set DirectoryName
     *
     * @access  public
     * @param   string directoryName
     */
    function setDirectoryName($directoryName) {
      $this->directoryName= $directoryName;
    }

    /**
     * Get DirectoryName
     *
     * @access  public
     * @return  string
     */
    function getDirectoryName() {
      return $this->directoryName;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        "%s(origin= %s) {\n".
        "  [classloader   ] %s\n".
        "  [implementation] %s\n".
        "  [interface     ] %s\n".
        "  [directoryName ] %s\n".
        "}",
        $this->getClassName(),
        $this->origin,
        $this->classloader->getClassName(),
        $this->implementation,
        $this->interface,
        $this->directoryName
      );
    }

  } implements(__FILE__, 'remote.server.deploy.Deployable');
?>

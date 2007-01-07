<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.server.deploy.Deployable');

  /**
   * Deployment
   *
   * @see      xp://remote.server.deploy.Deployable
   * @purpose  Deployment
   */
  class Deployment extends Object implements Deployable {
    public
      $origin           = '',
      $classloader      = NULL,
      $implementation   = '',
      $interface        = '',
      $directoryName    = '';
    
    /**
     * Constructor
     *
     * @param   string origin
     */
    public function __construct($origin) {
      $this->origin= $origin;
    }

    /**
     * Set Classloader
     *
     * @param   lang.Object classloader
     */
    public function setClassloader($classloader) {
      $this->classloader= $classloader;
    }

    /**
     * Get Classloader
     *
     * @return  lang.Object
     */
    public function getClassloader() {
      return $this->classloader;
    }

    /**
     * Set Implementation
     *
     * @param   string implementation
     */
    public function setImplementation($implementation) {
      $this->implementation= $implementation;
    }

    /**
     * Get Implementation
     *
     * @return  string
     */
    public function getImplementation() {
      return $this->implementation;
    }

    /**
     * Set Interface
     *
     * @param   string interface
     */
    public function setInterface($interface) {
      $this->interface= $interface;
    }

    /**
     * Get Interface
     *
     * @return  string
     */
    public function getInterface() {
      return $this->interface;
    }

    /**
     * Set DirectoryName
     *
     * @param   string directoryName
     */
    public function setDirectoryName($directoryName) {
      $this->directoryName= $directoryName;
    }

    /**
     * Get DirectoryName
     *
     * @return  string
     */
    public function getDirectoryName() {
      return $this->directoryName;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
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

  } 
?>

<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.sun.webstart.jnlp.JnlpResource');

  /**
   * JNLP resource that defines the J2SE version dependency
   *
   * XML representation
   * <pre>
   *   <j2se version="1.4+"/>
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpJ2seResource extends JnlpResource {
    var
      $version   = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string version
     */
    function __construct($version) {
      parent::__construct();
      $this->version= $version;
    }

    /**
     * Set version
     *
     * @access  public
     * @param   string version
     */
    function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get version
     *
     * @access  public
     * @return  string
     */
    function getVersion() {
      return $this->version;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    function getName() { 
      return 'j2se';
    }

    /**
     * Get attributes
     *
     * @access  public
     * @return  array
     */
    function getAttributes() { 
      return array('version' => $this->version);
    }
  
  }
?>

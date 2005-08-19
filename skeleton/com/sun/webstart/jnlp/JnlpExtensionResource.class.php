<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.sun.webstart.jnlp.JnlpResource');

  /**
   * JNLP resource that points to a component or installer extension 
   * that is required to run the app.
   *
   * XML representation:
   * <pre>
   *   <extension name="Java Help" href="lib/core/JavaHelp/JavaHelp.jnlp"/>
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpExtensionResource extends JnlpResource {
    var
      $name     = '',
      $href     = '',
      $version  = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string href
     * @param   string version
     */
    function __construct($name, $href, $version) {
      $this->name= $name;
      $this->href= $href;
      $this->version= $version;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set Href
     *
     * @access  public
     * @param   string href
     */
    function setHref($href) {
      $this->href= $href;
    }

    /**
     * Get Href
     *
     * @access  public
     * @return  string
     */
    function getHref() {
      return $this->href;
    }
    
    /**
     * Get JAR location
     *
     * @access  public
     * @return  string
     */
    function getLocation() {
      return $this->href.($this->version ? '?version-id='.$this->version : '');
    }

    /**
     * Set Version
     *
     * @access  public
     * @param   string version
     */
    function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
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
    function getTagName() { 
      return 'jar';
    }

    /**
     * Get attributes
     *
     * @access  public
     * @return  array
     */
    function getTagAttributes() { 
      return array_merge(
        array('href' => $this->href), 
        $this->version ? array('version' => $this->version) : array()
      );
    }
  }
?>

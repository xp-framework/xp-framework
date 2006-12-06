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
    public
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
    public function __construct($name, $href, $version) {
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
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Href
     *
     * @access  public
     * @param   string href
     */
    public function setHref($href) {
      $this->href= $href;
    }

    /**
     * Get Href
     *
     * @access  public
     * @return  string
     */
    public function getHref() {
      return $this->href;
    }
    
    /**
     * Get JAR location
     *
     * @access  public
     * @return  string
     */
    public function getLocation() {
      return $this->href.($this->version ? '?version-id='.$this->version : '');
    }

    /**
     * Set Version
     *
     * @access  public
     * @param   string version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @access  public
     * @return  string
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    public function getTagName() { 
      return 'extension';
    }

    /**
     * Get attributes
     *
     * @access  public
     * @return  array
     */
    public function getTagAttributes() { 
      return array_merge(
        array('href' => $this->href), 
        $this->name ? array('name' => $this->name) : array(),
        $this->version ? array('version' => $this->version) : array()
      );
    }
  }
?>

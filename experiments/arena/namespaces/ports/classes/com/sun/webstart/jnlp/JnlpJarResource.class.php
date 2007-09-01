<?php
/* This class is part of the XP framework
 *
 * $Id: JnlpJarResource.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::sun::webstart::jnlp;

  ::uses('com.sun.webstart.jnlp.JnlpResource');

  /**
   * JNLP resource that points to a JAR (Java Archive) file
   *
   * XML representation:
   * <pre>
   *   <jar href="lib/jbosssx-client.jar"/>
   * </pre>
   *
   * XML representation with version:
   * <pre>
   *   <jar href="./lib/util.jar" version="7.2.1.1.Build.12"/>
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpJarResource extends JnlpResource {
    public
      $href     = '',
      $version  = '';

    /**
     * Constructor
     *
     * @param   string href
     * @param   string version default NULL
     */
    public function __construct($href, $version= NULL) {
      $this->href= $href;
      $this->version= $version;
    }

    /**
     * Set Href
     *
     * @param   string href
     */
    public function setHref($href) {
      $this->href= $href;
    }

    /**
     * Get Href
     *
     * @return  string
     */
    public function getHref() {
      return $this->href;
    }
    
    /**
     * Get JAR location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->href.($this->version ? '?version-id='.$this->version : '');
    }

    /**
     * Set Version
     *
     * @param   string version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @return  string
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getTagName() { 
      return 'jar';
    }

    /**
     * Get attributes
     *
     * @return  array
     */
    public function getTagAttributes() { 
      return array_merge(
        array('href' => $this->href), 
        $this->version ? array('version' => $this->version) : array()
      );
    }
  }
?>

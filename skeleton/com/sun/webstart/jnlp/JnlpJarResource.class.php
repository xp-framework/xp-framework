<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.sun.webstart.jnlp.JnlpResource');

  /**
   * JNLP resource that points to a JAR (Java Archive) file
   *
   * XML representation
   * <pre>
   *   <jar href="lib/jbosssx-client.jar"/>
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpJarResource extends JnlpResource {
    var
      $href   = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string href
     */
    function __construct($href) {
      $this->href= $href;
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
      return array('href' => $this->href);
    }
  
  }
?>

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
    public
      $href   = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string href
     */
    public function __construct($href) {
      parent::__construct();
      $this->href= $href;
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
     * Get name
     *
     * @access  public
     * @return  string
     */
    public function getTagName() { 
      return 'jar';
    }

    /**
     * Get attributes
     *
     * @access  public
     * @return  array
     */
    public function getTagAttributes() { 
      return array('href' => $this->href);
    }
  
  }
?>

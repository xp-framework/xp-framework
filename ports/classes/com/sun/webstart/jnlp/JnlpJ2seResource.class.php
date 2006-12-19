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
   * XML representation with more than one version
   * <pre>
   *   <j2se version="1.4 1.3 1.2.2+"/>
   * </pre>
   *
   * XML representation with heap sizes:
   * <pre>
   *   <j2se version="1.3+" initial-heap-size="64m" max-heap-size="512m" />
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpJ2seResource extends JnlpResource {
    public
      $version          = '',
      $href             = '',
      $initialHeapSize  = 0,
      $maxHeapSize      = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string version
     * @param   string href default NULL
     * @param   int initialHeapSize default 0
     * @param   int maxHeapSize default 0
     */
    public function __construct($version, $href= NULL, $initialHeapSize= 0, $maxHeapSize= 0) {
      $this->version= $version;
      $this->href= $href;
      $this->initialHeapSize= $initialHeapSize;
      $this->maxHeapSize= $maxHeapSize;
    }

    /**
     * Set version
     *
     * @access  public
     * @param   string version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get version
     *
     * @access  public
     * @return  string
     */
    public function getVersion() {
      return $this->version;
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
     * Set InitialHeapSize
     *
     * @access  public
     * @param   int initialHeapSize
     */
    public function setInitialHeapSize($initialHeapSize) {
      $this->initialHeapSize= $initialHeapSize;
    }

    /**
     * Get InitialHeapSize
     *
     * @access  public
     * @return  int
     */
    public function getInitialHeapSize() {
      return $this->initialHeapSize;
    }

    /**
     * Set MaxHeapSize
     *
     * @access  public
     * @param   int maxHeapSize
     */
    public function setMaxHeapSize($maxHeapSize) {
      $this->maxHeapSize= $maxHeapSize;
    }

    /**
     * Get MaxHeapSize
     *
     * @access  public
     * @return  int
     */
    public function getMaxHeapSize() {
      return $this->maxHeapSize;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    public function getTagName() { 
      return 'j2se';
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
        $this->href ? array('href' => $this->href) : array(),
        $this->initialHeapSize ? array('initialHeapSize' => $this->initialHeapSize) : array(),
        $this->maxHeapSize ? array('maxHeapSize' => $this->maxHeapSize) : array()
      );
    }
  }
?>

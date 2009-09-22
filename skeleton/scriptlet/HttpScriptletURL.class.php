<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.Hashmap',
    'peer.URL'
  );
  
  /**
   * Represents a HTTP scriptlet URLs
   *
   * @see      xp://scriptlet.HttpScriptlet
   * @purpose  URL representation class
   */
  class HttpScriptletURL extends URL {
      
    protected
      $values= NULL;
  
    /**
     * Constructor
     *
     * @param string url The URL
     */
    public function __construct($url) {
      parent::__construct($url);
      
      // Setup hashmap
      $this->values= new Hashmap();
      
      // Extract information
      $this->extract();
    }
    
    /**
     * Extract information from URL
     *
     */
    protected function extract() {
      $this->setSessionId($this->getParam('psessionid'));
    }

    /**
     * Set session id
     *
     * @param string language The session
     */
    public function setSessionId($session) {
      $this->values->put('SessionId', $session);
      $this->setParam('psessionid', $session);
    }

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId() {
      return $this->values->get('SessionId');
    }

    /**
     * Returns string representation for the URL
     *
     * The URL is build by using sprintf() and the following
     * parameters:
     * <pre>
     * Ord Fill            Example
     * --- --------------- --------------------
     *   1 scheme          http
     *   2 host            host.foo.bar
     *   3 path            /foo/bar/index.html
     *   4 dirname(path)   /foo/bar/
     *   5 basename(path)  index.html
     *   6 query           a=b&b=c
     *   7 session id      cb7978876218bb7
     *   8 fraction        #test
     * </pre>
     *
     * @return string
     */
    public function getURL() {
      return sprintf(
        '%1$s://%2$s%3$s?%6$s&psessionid=%7$s',
        $this->getScheme(),
        $this->getHost(),
        $this->getPath(),
        dirname($this->getPath()),
        basename($this->getPath()),
        $this->getQuery(),
        $this->getSessionId(),
        $this->getFragment()
      );
    }
  }
?>

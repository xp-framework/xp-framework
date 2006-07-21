<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.URL');

  /**
   * References are what you declare with the @see Keyword.
   * These may be written in the following form:
   * <pre>
   *   protocol://foo.bar/path/to/document.extension?query#fragment
   * </pre>
   *
   * @deprecated
   * @see   xp-doc://README.DOC
   */
  class Reference extends Object {
    public 
      $link          = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str default NULL The string to parse from
     * @see     xp://text.apidoc.Reference#fromString
     */
    public function __construct($str= NULL) {
      if (NULL !== $str) $this->fromString($str);
      
    }
    
    /**
     * Retrieve valid schemes
     *
     * @model   static
     * @access  public
     * @return  string[] valid schemes
     */
    public function getValidSchemes() {
      return Reference::_schemes();
    }

    /**
     * Register scheme
     *
     * @model   static
     * @access  public
     * @param   string scheme the scheme to register
     * @return  string[] new valid schemes (incl. newly registered scheme)
     */
    public function registerScheme($scheme) {
      return Reference::_schemes($scheme);
    }
    
    /**
     * Return or register schemes
     *
     * @access  private
     * @param   string additionalScheme default NULL
     * @return  string[] schemes
     */
    public function _schemes($additionalScheme= NULL) {
      static $__schemes= array(
        'xp', 
        'xp-doc', 
        'php', 
        'php-gtk', 
        'http', 
        'https', 
        'ftp', 
        'mailto', 
        'rfc'
      );
      
      if (NULL !== $additionalScheme) $__schemes[]= $additionalScheme;
      return $__schemes;
    }
    
    /**
     * Parses a reference from a string
     *
     * @access  public
     * @param   string str The string to parse from
     * @throws  lang.FormatException in case the scheme is'nt recognized
     */
    public function fromString($str) {
      $u= &new URL(FALSE !== ($p= strpos($str, ' '))
        ? substr($str, 0, $p)
        : $str
      );
      $this->link= array(
        'scheme'   => $u->getScheme('xp'),  // Links without scheme are internal
        'user'     => $u->getUser(),
        'pass'     => $u->getPassword(),
        'host'     => $u->getHost(),
        'port'     => $u->getPort(),
        'path'     => $u->getPath(),
        'query'    => $u->getQuery(),
        'fragment' => $u->getFragment(),
        'description' => $p !== FALSE ? substr($str, $p+ 1) : NULL
      );
      
      
      if (in_array($this->link['scheme'], Reference::getValidSchemes())) return;
      throw(new FormatException('Scheme '.$this->link['scheme'].' not recognized'));
    }
  }
?>

<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * References are what you declare with the @see Keyword.
   * These may be written in the following form:
   * <pre>
   *   protocol://foo.bar/path/to/document.extension?query#fragment
   * </pre>
   *
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
     * @see     xp://lang.apidoc.Reference#fromString
     */
    public function __construct($str= NULL) {
      if (NULL !== $str) self::fromString($str);
      
    }
    
    /**
     * Retrieve valid schemes
     *
     * @model   static
     * @access  public
     * @return  string[] valid schemes
     */
    public static function getValidSchemes() {
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
    public static function registerScheme($scheme) {
      return Reference::_schemes($scheme);
    }
    
    /**
     * Return or register schemes
     *
     * @access  private
     * @param   string additionalScheme default NULL
     * @return  string[] schemes
     */
    private function _schemes($additionalScheme= NULL) {
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
     * @throws  FormatException in case the scheme is'nt recognized
     */
    public function fromString($str) {
      if (FALSE !== ($p= strpos($str, ' '))) {
        $this->link= parse_url(substr($str, 0, $p));
        $this->link['description']= substr($str, $p+ 1);
      } else {
        $this->link= parse_url($str);
        $this->link['description']= NULL;
      }
      
      // Links without scheme are internal
      if (empty($this->link['scheme'])) {
        $this->link['scheme']= 'xp';
      }
      
      if (in_array($this->link['scheme'], Reference::getValidSchemes())) return;
      throw (new FormatException('Scheme '.$this->link['scheme'].' not recognized'));
    }
  }
?>

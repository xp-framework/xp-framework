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
    var 
      $link = array();
      
    var
      $_validSchemes = array('xp', 'xp-doc', 'php', 'php-gtk', 'http', 'https', 'ftp', 'mailto', 'rfc');
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string str default NULL The string to parse from
     * @see     xp://lang.apidoc.Reference#fromString
     */
    function __construct($str= NULL) {
      if (NULL !== $str) $this->fromString($str);
      parent::__construct();
    }
    
    /**
     * Parses a reference from a string
     *
     * @access  public
     * @param   string str The string to parse from
     * @throws  FormatException in case the scheme is'nt recognized
     */
    function fromString($str) {
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
      
      if (in_array($this->link['scheme'], $this->_validSchemes)) return;
      throw(new FormatException('Scheme '.$this->link['scheme'].' not recognized'));
    }
  }
?>

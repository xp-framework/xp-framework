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
   * @see xp-doc:README.DOC
   */
  class Reference extends Object {
    var 
      $link;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string str default NULL The string to parse from
     * @see     #fromString
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
     */
    function fromString($str) {
      $this->link= parse_url($str);
      
      // Links without scheme are internal
      if (!isset($this->link['scheme'])) {
        $this->link['scheme']= 'xp';
      }
    }
  }
?>

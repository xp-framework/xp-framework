<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Class to aid website internationalization based on the
   * Accept-Language and Accept-Charset headers.
   *
   * Basic usage example:
   * <code>
   *   uses('org.apache.LocaleNegotiator');
   *
   *   $l= &new LocaleNegotiator(
   *     'de-at, de;q=0.75, en-us;q=0.50, en;q=0.25',
   *     'ISO-8859-1,utf-8;q=0.7,*;q=0.7'
   *   );
   *   var_dump(
   *     $l, 
   *     $l->getLocale($supported= array('de_DE', 'en_US')),
   *     $l->getCharset($supported= array('iso-8859-1'))
   *   );
   * </code>
   *
   * @see      http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
   * @purpose  Negotiate locales
   */
  class LocaleNegotiator extends Object {
    var
      $languages    = array(),
      $charsets     = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string languages
     * @param   string charset
     */
    function __construct($languages, $charsets) {
      $this->languages= $this->_parse($languages);
      $this->charsets= $this->_parse($charsets);
      parent::__construct();
    }
    
    /**
     * Retreive locale
     *
     * @access  public
     * @param   string[] supported
     * @param   string default default NULL
     * @return  string locale or default if none matches
     */
    function getLocale($supported, $default= NULL) {
      $chosen= FALSE;
      foreach ($this->languages as $lang => $q) {
        if (
          ($chosen= $this->_find($lang, $supported)) ||
          ($chosen= $this->_find($lang, $supported, 2))
        ) break;
      }
      return $chosen ? $chosen : $default;
    }
    
    /**
     * Retreive charset
     *
     * @access  public
     * @param   string[] supported
     * @param   string default default NULL
     * @return  string charset or default if none matches
     */
    function getCharset($supported, $default= NULL) {
      $chosen= FALSE;
      foreach ($this->charsets as $charset => $q) {
        if ($chosen= $this->_find($charset, $supported)) break;
      }
      return $chosen ? $chosen : $default;
    }
    
    /**
     * Private helper that parses a string of the following format:
     *
     * <pre> 
     * Accept-Language: en,de;q=0.5
     * Accept-Language: en-UK;q=0.7, en-US;q=0.6, no;q=1.0, dk;q=0.8
     * Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
     * </pre>
     *
     * @access  private
     * @param   
     * @return  
     */
    function _parse($str) {
      $values= array();
      if ($t= strtok($str, ', ')) do {
        if (FALSE === ($p= strpos($t, ';'))) {
          $value= $t;
          $q= 1.0;
        } else {
          $value= substr($t, 0, $p);
          $q= (float)substr($t, $p + 3);    // skip ";q="
        }
        $values[strtolower($value)]= $q;
      } while ($t= strtok(', '));
      
      asort($values, SORT_NUMERIC);
      return array_reverse($values);
    }
    
    /**
     * Private helper that searches an array using strncasecmp as comparator
     *
     * @see     php://strncasecmp
     * @access  private
     * @param   string value
     * @param   string[] array
     * @param   int len default -1
     * @return  string found or FALSE to indicate it wasn't found
     */
    function _find($value, $array, $len= -1) {
      if (-1 == $len) $len= strlen($value);
      foreach ($array as $cmp) {
        if (0 == strncasecmp($value, $cmp, $len)) return $cmp;
      }
      return FALSE;
    }
  }
?>

<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Locale');

  /**
   * Class to aid website internationalization based on the
   * Accept-Language and Accept-Charset headers.
   *
   * Basic usage example:
   * <code>
   *   uses('scriptlet.LocaleNegotiator');
   *
   *   $negotiator= new LocaleNegotiator(
   *     'de-at, de;q=0.75, en-us;q=0.50, en;q=0.25',
   *     'ISO-8859-1,utf-8;q=0.7,*;q=0.7'
   *   );
   *   var_dump(
   *     $negotiator, 
   *     $negotiator->getLocale(
   *       $supported= array('de_DE', 'en_US'), 
   *       $default= 'de_DE'
   *     ),
   *     $negotiator->getCharset(
   *       $supported= array('iso-8859-1', 'utf-8'),
   *       $default= 'iso-8859-1'
   *     )
   *   );
   * </code>
   * 
   * Within a scriptlet, use the getHeader() method of the request
   * object to retrieve the values of the Accept-Language / Accept-Charset
   * headers and the setHeader() method of the response object to
   * indicate language negotation has took place.
   *
   * Abbreviated example:
   * <code>
   *   function doGet($req, $res) {
   *     $negotiator= new LocaleNegotiator(
   *       $req->getHeader('Accept-Language'), 
   *       $req->getHeader('Accept-Charset')
   *     );
   *     $locale= $negotiator->getLocale(array('de_DE', 'en_US'), 'de_DE');
   *
   *     // [... Do whatever needs to be done for this language ...]
   *
   *     $res->setHeader('Content-Language', $locale->getLanguage());
   *     $res->setHeader('Vary', 'Accept-Language');
   *   }
   * </code>
   *
   * @see      http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
   * @purpose  Negotiate locales
   */
  class LocaleNegotiator extends Object {
    public
      $languages    = array(),
      $charsets     = array();

    /**
     * Constructor
     *
     * @param   string languages
     * @param   string charset
     */
    public function __construct($languages, $charsets) {
      $this->languages= $this->_parse($languages);
      $this->charsets= $this->_parse($charsets);
      
    }
    
    /**
     * Retrieve locale
     *
     * @param   string[] supported
     * @param   string default default NULL
     * @return  util.Locale
     */
    public function getLocale($supported, $default= NULL) {
      $chosen= FALSE;
      foreach ($this->languages as $lang => $q) {
        if (
          ($chosen= $this->_find($lang, $supported)) ||
          ($chosen= $this->_find($lang, $supported, 2))
        ) break;
      }
      return new Locale($chosen ? $chosen : $default);
    }
    
    /**
     * Retrieve charset
     *
     * @param   string[] supported
     * @param   string default default NULL
     * @return  string charset or default if none matches
     */
    public function getCharset($supported, $default= NULL) {
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
     * @param   string str
     * @return  array values
     */
    protected function _parse($str) {
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
     * @param   string value
     * @param   string[] array
     * @param   int len default -1
     * @return  string found or FALSE to indicate it wasn't found
     */
    protected function _find($value, $array, $len= -1) {
      if (-1 == $len) $len= strlen($value);
      foreach ($array as $cmp) {
        if (0 == strncasecmp($value, $cmp, $len)) return $cmp;
      }
      return FALSE;
    }
  }
?>

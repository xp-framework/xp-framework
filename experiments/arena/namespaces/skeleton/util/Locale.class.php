<?php
/* This class is part of the XP framework
 *
 * $Id: Locale.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util;

  /**
   * Locale
   * 
   * Usage [retreiving default locale]
   * <code>
   *   $locale= &Locale::getDefault();
   *   var_dump($locale);
   * </code>
   *
   * Usage [setting default locale]
   * <code>
   *   Locale::setDefault(new Locale('de_DE'));
   * </code>
   *
   * @see      http://ftp.ics.uci.edu/pub/ietf/http/related/iso639.txt
   * @see      http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
   * @see      http://groups.google.com/groups?threadm=DREPPER.96Aug8030605%40i44d2.ipd.info.uni-karlsruhe.de#link1
   * @purpose  Represent a locale
   */
  class Locale extends lang::Object {
    public
      $lang     = '',
      $country  = '',
      $variant  = '';
    
    public
      $_str     = '';

    /**
     * Constructor
     *
     * @param   string lang 2-letter abbreviation of language
     * @param   string country 2-letter abbreviation of country
     * @param   string variant default ''
     */
    public function __construct() {
      switch (func_num_args()) {
        case 1: 
          $this->_str= func_get_arg(0);
          sscanf(func_get_arg(0), '%2s_%2s%s', $this->lang, $this->country, $this->variant);
          break;
          
        case 2:
          list($this->lang, $this->country)= func_get_args();
          $this->_str= $this->lang.'_'.$this->country;
          break;
          
        case 3:
          list($this->lang, $this->country, $this->variant)= func_get_args();
          $this->_str= $this->lang.'_'.$this->country.'@'.$this->variant;
          break;
      }
    }
    
    /**
     * Get default locale
     *
     * @return  util.Locale
     */
    public static function getDefault() {
      return new (('C' == ($locale= setlocale(LC_ALL, NULL)) 
        ? 'en_US'
        : $locale
      ));
    }
    
    /**
     * Set default locale for this script
     *
     * @param   util.Locale locale
     * @throws  lang.IllegalArgumentException in case the locale is not available
     */
    public static function setDefault($locale) {
      if (FALSE === setlocale(LC_ALL, $locale->toString())) {
        throw(new lang::IllegalArgumentException(sprintf(
          'Locale [lang=%s,country=%s,variant=%s] not available',
          $this->lang, 
          $this->country, 
          ltrim($this->variant, '.@')
        )));
      }
    }

    /**
     * Get Language
     *
     * @return  string
     */
    public function getLanguage() {
      return $this->lang;
    }

    /**
     * Get Country
     *
     * @return  string
     */
    public function getCountry() {
      return $this->country;
    }

    /**
     * Get Variant
     *
     * @return  string
     */
    public function getVariant() {
      return $this->variant;
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return sprintf('%u', crc32($this->_str));
    }
    
    /**
     * Create string representation
     *
     * Examples:
     * <pre>
     * de_DE
     * en_US
     * de_DE@euro
     * de_DE.ISO8859-1
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return $this->_str;
    }
  }
?>

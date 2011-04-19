<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'util';

  /**
   * Locale
   * 
   * Usage [retreiving default locale]
   * <code>
   *   $locale= util·Locale::getDefault();
   *   var_dump($locale);
   * </code>
   *
   * Usage [setting default locale]
   * <code>
   *   util·Locale::setDefault(new util·Locale('de_DE'));
   * </code>
   *
   * @see      http://ftp.ics.uci.edu/pub/ietf/http/related/iso639.txt
   * @see      http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
   * @see      http://groups.google.com/groups?threadm=DREPPER.96Aug8030605%40i44d2.ipd.info.uni-karlsruhe.de#link1
   * @purpose  Represent a locale
   */
  class util·Locale extends Object {
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
      return new self(('C' == ($locale= setlocale(LC_ALL, NULL)) 
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
        throw new IllegalArgumentException(sprintf(
          'Locale [lang=%s,country=%s,variant=%s] not available',
          $locale->lang, 
          $locale->country, 
          ltrim($locale->variant, '.@')
        ));
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
     * Returns whether a given object is equal to this locale.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->_str === $cmp->_str;
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

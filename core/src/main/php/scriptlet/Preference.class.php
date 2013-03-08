<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A preference
   *
   * Examples
   * ~~~~~~~~
   * <pre> 
   * Accept-Language: en,de;q=0.5
   * Accept-Language: en-UK;q=0.7, en-US;q=0.6, no;q=1.0, dk;q=0.8
   * Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
   * </pre>
   *
   * Usage
   * ~~~~~
   * <code>
   *   $preferred= new Preference('text/xml,text/plain;q=0.8');
   *   $supported= array('text/plain', 'text/html');
   *   
   *   $best= $preferred->match($supported);  // "text/html"
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.scriptlet.PreferenceTest
   */
  class Preference extends Object {
    protected $list= array();

    /**
     * Parses the "Accept" header
     *
     * @param  string[] accept
     * @param  [:float] list
     */
    protected function preferenceOf($accept) {
      $values= array();
      $prec= 1.0;
      foreach ($accept as $t) {
        preg_match('# ?(.+); ?q=([0-9\.]+)#', $t, $matches);
        if (empty($matches)) {
          $values[trim($t, ' ')]= $prec - 0.00001 * substr_count($t, '*') + 0.0001 * substr_count($t, ';');
          $prec-= 0.000001;
        } else {
          $values[$matches[1]]= (float)$matches[2];
        }
      }
      
      arsort($values, SORT_NUMERIC);
      return $values;
    }

    /**
     * Constructor
     *
     * @param   var arg
     */
    public function __construct($arg) {
      if (is_array($arg)) {
        $this->list= $this->preferenceOf($arg);
      } else {
        $this->list= $this->preferenceOf(explode(',', $arg));
      }
    }

    /**
     * ValueOf factory method
     *
     * @param   var arg
     * @return  self
     */
    public static function valueOf($arg) {
      return new self($arg);
    }

    /**
     * Returns all preferred values in order of quality-rating
     *
     * @return  string[]
     */
    public function all() {
      return array_keys($this->list);
    }

    /**
     * Match this preference
     *
     * @param  string[] supported
     * @param  string
     */
    public function match($supported) {
      $str= implode(' ', $supported);
      foreach ($this->list as $preference => $q) {
        if (preg_match('#('.strtr(preg_quote($preference, '#'), array('\*' => '[^ ]+')).')#', $str, $matches)) return $matches[1];
      }
      return NULL;
    }

    /**
     * Returns quality of a given type
     *
     * @param  string type
     * @param  int precision 
     * @return double
     */
    public function qualityOf($type, $precision= 3) {
      if (isset($this->list[$type])) {
        $q= $this->list[$type];
      } else {
        $q= 0.0;
        foreach ($this->list as $preference => $q) {
          if (preg_match('#('.strtr(preg_quote($preference, '#'), array('\*' => '[^ ]+')).')#', $type, $matches)) break;
        }
      }
      return round($q, $precision);
    }

    /**
     * Returns whether another instance is equal to this
     *
     * @param  var cmp
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->list === $this->list;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      $list= '';
      foreach ($this->list as $preference => $q) {
        $list.= (1.0 - $q < 0.001) ? ', '.$preference : sprintf(', %s;q=%.1f', $preference, $q);
      }
      return $this->getClassName().'<'.substr($list, 2).'>';
    }
  }
?>

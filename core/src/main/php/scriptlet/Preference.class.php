<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A preference
   *
   * <pre> 
   * Accept-Language: en,de;q=0.5
   * Accept-Language: en-UK;q=0.7, en-US;q=0.6, no;q=1.0, dk;q=0.8
   * Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
   * </pre>
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
          $values[trim($t, ' ')]= $prec - 0.0001 * substr_count($t, '*') + 0.001 * substr_count($t, ';');
          $prec-= 0.00001;
        } else {
          $values[$matches[1]]= (float)$matches[2];
        }
      }
      
      arsort($values, SORT_NUMERIC);
      return $values;
    }

    /**
     * Constructor.
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
     * Returns whether another instance is equal to this
     *
     * @param  var cmp
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->list === $this->list;
    }
  }
?>

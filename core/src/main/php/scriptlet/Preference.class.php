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
      $significance= 1.0;
      foreach ($accept as $t) {
        if (FALSE === ($p= strpos($t, ';'))) {
          $value= ltrim($t, ' ');
          $q= $significance-= 0.0001;
        } else {
          $value= ltrim(substr($t, 0, $p), ' ');
          $q= (float)ltrim(substr($t, $p), '; q=');
        }
        $values[$value]= $q;
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

    public function preferred() {
      return key($this->list);
    }

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
      foreach ($this->list as $preference => $q) {
        if (FALSE !== ($p= array_search($preference, $supported))) return $supported[$p];
      }
      return NULL;
    }
  }
?>

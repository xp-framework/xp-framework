<?php namespace net\xp_framework\unittest\core\generics;

/**
 * Lookup map
 *
 */
#[@generic(self= 'K, V', implements= array('K, V'))]
abstract class NSAbstractDictionary extends \lang\Object implements NSIDictionary {
  
  /**
   * Constructor
   *
   * @param   array<string, var> initial
   */
  public function __construct($initial= array()) {
    foreach ($initial as $key => $value) {
      $this->put($key, $value);
    }
  }
}
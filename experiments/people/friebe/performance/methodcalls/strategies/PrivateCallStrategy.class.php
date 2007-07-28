<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PrivateCallStrategy extends Object {

    private function privateMethod($i) {
      $i++;
    }
  
    public function run($times) {
      for ($i= 0; $i < $times; $i++) {
        $this->privateMethod($i);
      }
    }
  }
?>

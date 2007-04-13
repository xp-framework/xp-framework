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
  class AntEnvironment extends Object {
    public
      $in   = NULL,
      $out  = NULL,
      $err  = NULL;

    public function __construct($out, $err) {
      $this->out= $out;
      $this->err= $err;
    }
  }
?>

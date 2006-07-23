<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // class StringBuffer {
  //   protected
  //     $_buf= '';
  //
  //   public self append($str) {
  //     $this->_buf.= $str;
  //     return $this;
  //   }
  // }
  //
  // echo new StringBuffer()->append('Hello')->toString();
  // }}}

  // {{{ Generated version
  class StringBuffer extends xp·lang·Object {
    protected
      $_buf= '';

    public function append($str) {
      $this->_buf.= $str;
      return $this;
    }
  }
    
  echo xp::create(new StringBuffer())->append('Hello')->toString();
  // }}}
?>

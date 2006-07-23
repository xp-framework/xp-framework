<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // class String {
  //   protected 
  //     $_buf= '',
  //     $_len= 0;
  // 
  //   public 
  //     property $buffer get $_buf set setBuffer(),
  //     property $length get $_len set void;
  // 
  //   public void setBuffer($b) {
  //     $this->_buf= $b;
  //     $this->_len= strlen($b);
  //     echo '>>> setBuffer() called {_buf= "'.$this->_buf.'", _len= '.$this->_len."}\n";
  //   }
  // }
  //
  // $s= new String();
  // $s->buffer= 'Hello';          // calls setBuffer() with 'Hello' as argument    
  // echo "'".$s->buffer."'";      // reads member "_buf"
  // echo ' ('.$s->length.')';     // reads member "_len"
  //
  // try {
  //   $s->length++;
  // } catch (xp~lang~IllegalAccessException $e) {
  //   // Expected
  //   echo ', caught expected ', $e->getClassName();
  // }
  // }}}


  // {{{ Generated version (dynamic/generic model)
  class String extends xp·lang·Object {
    protected 
      $_buf= '',
      $_len= 0;
    
    public function setBuffer($b) {
      $this->_buf= $b;
      $this->_len= strlen($b);
      echo '>>> setBuffer() called {_buf= "'.$this->_buf.'", _len= '.$this->_len."}\n";
    }
    
    public static $__properties= array(
      'buffer' => array('$_buf', 'setBuffer'),
      'length' => array('$_len', NULL)
    );
    
    function __get($name) {
      if (!isset(self::$__properties[$name])) die('Read of non-existant property "'.$name.'"');
      if (NULL === self::$__properties[$name][0]) {
        throw xp::exception(new xp·lang·IllegalAccessException('Cannot access property "'.$name.'"'));
      } else if ('$' == self::$__properties[$name][0][0]) {
        return $this->{substr(self::$__properties[$name][0], 1)};
      } else {
        return $this->{self::$__properties[$name][0]}();
      }
    }
    
    function __set($name, $value) {
      if (!isset(self::$__properties[$name])) die('Write of non-existant property "'.$name.'"');
      if (NULL === self::$__properties[$name][1]) {
        throw xp::exception(new xp·lang·IllegalAccessException('Cannot access property "'.$name.'"'));
      } else if ('$' == self::$__properties[$name][1][0]) {
        $this->{substr(self::$__properties[$name][1], 1)}= $value;
      } else {
        $this->{self::$__properties[$name][1]}($value);
      }
    }
  }

  $s= new String();
  $s->buffer= 'Hello';          // calls setBuffer() with 'Hello' as argument    
  echo "'".$s->buffer."'";      // reads member "_buf"
  echo ' ('.$s->length.')';     // reads member "_len"
  
  try {
    $s->length++;
  } catch (XPException $__e) {
    if ($__e->cause instanceof xp·lang·IllegalAccessException) {
      $e= $__e->cause;
      // Expected
      echo ', caught expected ', $e->getClassName();
    } else {
      throw $__e;
    }
  }
  // }}}
?>

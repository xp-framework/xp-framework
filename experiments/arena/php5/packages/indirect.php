<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ package lang
  package lang {
    class Object { }
    class UserClass extends lang~Object {
      public $name= '';
      
      protected function __construct($name) {
        $this->name= $name;
      }
      public static function forName($name) {
        return new self($name);
      }
      public function newInstance() {
        return new $this->name();
      }
    }
  }
  // }}}

  // {{{ package text
  package text {
    class String extends lang~Object { }
  }
  // }}}
  
  // {{{ main
  var_dump(lang~UserClass::forName('text~String')->newInstance());
  // }}}
?>

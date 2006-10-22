<?php
  require('lang.base.php');
  require('using.php');
  xp::sapi('cli');
  
  // {{{ Transaction "mock"
  class Transaction extends Object {
    var
      $conn= NULL,
      $name= '';

    function __construct($name) {
      $this->name= $name;
    }

    function __exit($e) {
      $this->conn->query(($e ? 'rollback ' : 'commit ').$this->name);
    }
  }
  // }}}
  
  // {{{ DBConnection "mock"
  class DBConnection extends Object {

    function &begin(&$t) {
      if (0 == strncmp('fail', $t->name, 4)) {
        return throw(new Exception(substr($t->name, 5)));
      }

      $this->query('begin transaction '.$t->name);
      $t->conn= &$this;
      return $t;
    }
    
    function query($sql) {
      if (0 == strncmp('fail', $sql, 4)) {
        return throw(new Exception(substr($sql, 5)));
      }
      
      Console::writeLine('     >> [SQL] ', $sql);
    }
  }
  // }}}
  
  // {{{ main
  $conn= &new DBConnection();
  
  Console::writeLine('===> Transaction ');
  try(); {
    using($conn->begin(new Transaction('ok')), $t, '{
      $conn->query("insert ...");
      $conn->query("update ...");
    }');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  Console::writeLine('---> OK');

  Console::writeLine('===> Exception');
  try(); {
    using($conn->begin(new Transaction('exception')), $t, '{
      $conn->query("insert ...");
      $conn->query("fail error #1205");
    }');
  } if (catch('Exception', $e)) {
    Console::writeLine('---> OK E: ', $e->getMessage());
  }

  Console::writeLine('===> Fail');
  try(); {
    using($conn->begin(new Transaction('fail transaction log full')), $t, '{
      $conn->query("insert ...");
      $conn->query("update ...");
    }');
  } if (catch('Exception', $e)) {
    Console::writeLine('---> OK E: ', $e->getMessage());
  }

  Console::writeLine('===> Null');
  try(); {
    using($transaction= NULL, $t, '{
      $conn->query("insert ...");
      $conn->query("update ...");
    }');
  } if (catch('NullPointerException', $e)) {
    Console::writeLine('---> OK NPE: ', $e->getMessage());
  }

  Console::writeLine('===> Shadowing');
  $t= TRUE;
  try(); {
    using($conn->begin(new Transaction('ok')), $t, '{
      $conn->query("insert ...");
      $conn->query("update ...");
    }');
  } if (catch('NullPointerException', $e)) {
    Console::writeLine('---> OK NPE: ', $e->getMessage());
  }
  // }}}
?>

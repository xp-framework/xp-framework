<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  package http {
    class Connection { }
  }

  package soap {
    import http~Connection;

    class Client {
      protected
        $conn   = NULL;

      public function __construct(Connection $conn) {
        $this->conn= $conn;
      }
    }
  }
  

  // {{{ main
  var_dump(new soap~Client(new http~Connection()));
  // }}}
?>

<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'rdbms.DriverManager', 
    'util.Date',
    'util.log.Logger',
    'util.log.ColoredConsoleAppender'
  );
  
  // {{{ main
  $p= new ParamString();
  
  // Set up logger
  if ($p->exists('debug')) {
    Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  }

  $conn= DriverManager::getConnection('sqlite://thekid@test/?log=default');
  try {
    $conn->connect();
    
    // Create table
    $conn->query('create table test (
      id INTEGER primary key,
      name varchar(255) not null,
      lastchange timestamp null,
      changedby varchar(255) default "user"
    )');
    
    // Insert two records
    $conn->insert(
      'into test (name, lastchange, changedby) values (%s, %s, %s)',
      'Timm\'s question was: "Is that a real \n?"',
      Date::now(),
      'timm'
    );
    $conn->insert(
      'into test (name, lastchange) values (%s, %s)',
      'KrokerdilBot',
      NULL
    );
    
    // Select
    $result= $conn->query('select int(id) id, name, date(lastchange) "lastchange", changedby from test');
    Console::writeLine('Result: ', var_export($result, 1));
    while ($record= $result->next()) {
      Console::writeLinef(
        "Record (id=%d) {\n".
        "  [name      ] %s\n".
        "  [lastchange] %s\n".
        "  [changedby ] %s\n".
        "}",
        $record['id'],
        $record['name'],
        $record['lastchange'] instanceof Date ? $record['lastchange']->toString() : 'NULL',
        $record['changedby']
      );
    }
  } catch (SQLException $e) {
    $e->printStackTrace();
  } finally(); {
    $conn->query('drop table test');
    $conn->close();
    $e && exit(-1);
  }
  
  Console::writeLine('Done');
  // }}}
?>

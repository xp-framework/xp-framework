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
      percentage float(3) not null,
      lastchange timestamp null,
      changedby varchar(255) default "user"
    )');
    
    // Insert two records
    $conn->insert(
      'into test (name, percentage, lastchange, changedby) values (%s, %f, %s, %s)',
      'Timm\'s question was: "Is that a real \n?"',
      6.1,
      Date::now(),
      'timm'
    );
    $conn->insert(
      'into test (name, percentage, lastchange) values (%s, %f, %s)',
      'KrokerdilBot',
      99.6,
      NULL
    );
    
    // Select
    $result= $conn->query('
      select 
        cast(id, "int") id, 
        name, 
        cast(percentage, "float") percentage,
        cast(lastchange, "date") lastchange, 
        changedby
      from 
        test
    ');
    Console::writeLine('Result: ', var_export($result, 1));
    while ($record= $result->next()) {
      Console::writeLinef(
        "Record {\n".
        "  [%-9s id        ] %d\n".
        "  [%-9s name      ] %s\n".
        "  [%-9s percentage] %.2f\n".
        "  [%-9s lastchange] %s\n".
        "  [%-9s changedby ] %s\n".
        "}",
        xp::typeOf($record['id']),
        $record['id'],
        xp::typeOf($record['name']),
        $record['name'],
        xp::typeOf($record['percentage']),
        $record['percentage'],
        xp::typeOf($record['lastchange']),
        $record['lastchange'] instanceof Date ? $record['lastchange']->toString() : 'NULL',
        xp::typeOf($record['changedby']),
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

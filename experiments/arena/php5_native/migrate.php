<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('net.xp_framework.tophp5.MigrationDoclet');
  
  $help= <<<__
Subjective: Migrate PHP4 classes and scripts using XP to PHP

* Replace try(); with try
* Replace if (catch('EXCEPTION_CLASS_NAME', \$e)) with 
  catch (EXCEPTION_CLASS_NAME \$e)
* Add correct namespace to new CLASS_NAME
* Add correct namespace to extends CLASS_NAME
* Add correct namespace to static CLASS_NAME::METHOD_NAME calls
* Add package statement around classes
* Rewrite implements(__FILE__, 'IMPLEMENTED_INTERFACE') to 
  implements IMPLEMENTED_INTERFACE
* Replace return throw() with throw()
* Replace class INTERFFACE_NAME extends Interface with
  interface INTERFFACE_NAME
* Remove method body from interface methods

Usage:
php migrate.php <<fully_qualified_class_name>>
__;


  // {{{ main
  $p= &new ParamString();
  if ($p->exists('help', '?')) {
    Console::writeLine($help);
    exit(1);
  }

  try(); {
    $doclet= &new MigrationDoclet();
    RootDoc::start($doclet, $p);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  $output= $doclet->getOutput();
  
  Console::writeLine($output);
  // }}}
?>

<?php
/* This file is part of the XP framework's people experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');

  // {{{ public class ClassCastException
  //     Indicates a cast failed
  class ClassCastException extends Exception { }
  // }}}

  // {{{ public class ParentElement
  //     For demonstration of up/downcasting
  class ParentElement extends Object { 
    var $parent_id= 0;
  }
  // }}}

  // {{{ public class ChildElement
  //     For demonstration of up/downcasting
  class ChildElement extends ParentElement { 
    var $child_id= 0;
  }
  // }}}
  
  // {{{ public class Container
  //     For demonstration of references
  class Container extends Object {
    var $child= NULL;
  }
  // }}}

  // {{{ public class SortedContainer
  //     For demonstration of references
  class SortedContainer extends Container {
  }
  // }}}

  // {{{ public class Foo
  //     For demonstration of ClassCastException
  class Foo extends Object { }
  // }}}

  // {{{ bool is_parent(&lang.Object o, string type)
  //     Retrieves whether specified object is parent class of type
  function is_parent(&$o, $type) {
    $c= get_class($o);
    do {
      if (!$type= get_parent_class($type)) return FALSE;
    } while ($c != $type);
    return TRUE;
  }
  // }}}

  // {{{ &mixed __cast__(&lang.Object o, string type)
  //     Casts an object to a specified type
  function &__cast__(&$o, $type) {
    if (
      !is_a($o, $type) &&           // Upcast
      !is_parent($o, $type)         // Downcast
    ) {
      return throw(new ClassCastException(xp::typeOf($o).' cannot be casted to a '.$type));
    }
    
    if (!is_object($c= unserialize('O:'.strlen($type).':"'.$type.'":0:{}'))) {
      return throw(new ClassCastException('Cannot create '.$type.' object'));
    }
    foreach (array_keys(get_object_vars($o)) as $var) {
      $c->$var= &$o->$var;
    }
    return $c;
  }
  // }}}

  // {{{ main
  
  // 1) Try upcasting (child to parent)
  Console::write('===> cast(new ChildElement(), ParentElement): ');
  $c= &new ChildElement();
  Console::write(var_export($c, 1), var_export(__cast__($c, 'ParentElement'), 1));
  Console::writeLine();

  // 2) Try downcasting (parent to child)
  Console::write('===> cast(new ParentElement(), ChildElement): ');
  $p= &new ParentElement();
  Console::write(var_export($p, 1), var_export(__cast__($p, 'ChildElement'), 1));
  Console::writeLine();
  
  // 3) Try casting to an invalid target class
  Console::write('===> cast(new ChildElement(), Foo): ');
  $f= &new ChildElement();
  try(); {
    Console::write(var_export($f, 1));
    __cast__($f, 'Foo');
  } if (catch('ClassCastException', $e)) {
    $e->printStackTrace();
  }
  Console::writeLine();
  
  // 4a) Demonstrate references kept
  $container= &new Container();
  $container->child= &new ChildElement();
  $sorted= &__cast__($container, 'SortedContainer');
  Console::writeLine('container= ', var_export($container, 1), ' sorted= ', var_export($sorted, 1));
  
  // 4b) Now, change original container
  $container->child->parent_id= 1;
  Console::writeLine('container= ', var_export($container, 1), ' sorted= ', var_export($sorted, 1));
  
  // }}}
?>

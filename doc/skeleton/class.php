<?php
  require('lang.base.php');
  uses('io.File');
  
  $f= &new File();
  $c= &$f->getClass();
  $parent= &$c->getParentclass();
  var_dump(
    $c,
    $c->getName(),
	$c->getMethods(),
	$c->getFields(),
	$c->hasMethod('toString')
  );
  var_dump(
    $parent,
    $parent->getName(),
	$parent->getMethods(),
	$parent->getFields(),
	$parent->hasMethod('toString')
  );
  var_dump(XPClass::getClasses());
  try(); {
    $c= &XPClass::forName('io.File') &&
	$o= &$c->newInstance();
  } if (catch ('ClassNotFoundException', $e)) {
    $e->printStackTrace();
	$o= NULL;
  }
  var_dump($o);
?>

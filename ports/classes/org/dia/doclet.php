<?php
  require('lang.base.php');
  uses('text.doclet.RootDoc', 'util.cmd.Console');

  $P= &new ParamString();
  if (!$P->exists('doclet')) {
    die('Parameter "--doclet" is required!');
  } else {
    $doclet_name= $P->value('doclet');
    try (); {
      $Class= &XPClass::forName($doclet_name);
      $Instance= &$Class->newInstance();
      if (!is('Doclet', $Instance)) 
        throw(new IllegalArgumentException('Given classname is not a "Doclet" class!'));
    } if (catch('Exception', $e)) {
      die($e->toString());
    }

    // remove '--doclet' argument and pass the rest to RootDoc
    while (list($key, $val)= each($P->list)) {
      if (0 == strncmp('--doclet', $val, 7)) continue;
      $list[$key]= $val;
    }
    RootDoc::start($Instance, new ParamString($list));
  }
?>

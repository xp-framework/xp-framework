<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');

  // {{{ main
  $DEBUG= TRUE;
  $annotation= (isset($argv[1]) 
    ? $argv[1] 
    : '[@test, @webmethod(name = \'Hello\'), @restricted(roles= array(\'admin\', \'root\')), @deprecated(\'Use new method X instead\')]'
  );
  var_dump($annotation);

  $src= preg_replace(
    array(
      '#@([a-z_]+),#',
      '#@([a-z_]+)\(\'([^\']+)\'\)#',
      '#@([a-z_]+)\(#',
      '#([a-z_]+) *= *#',
    ),
    array(
      '\'$1\' => NULL,',
      '\'$1\' => \'$2\'',
      '\'$1\' => array(',
      '\'$1\' => ',
    ),
    trim($annotation, '[]').','
  );
  $DEBUG && var_dump($src);
  var_dump(eval('return array('.$src.');'));
  // }}}
?>

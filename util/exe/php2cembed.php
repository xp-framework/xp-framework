<?php
/* Create executables
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'io.File', 
    'io.FileUtil', 
    'util.cmd.ParamString'
  );

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s scriptname.php > scriptname.c\n", basename($p->value(0)));
    exit();
  }
  $script= basename($p->value(1));
  
  try(); {
    $src= FileUtil::getContents(new File($p->value(1)));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $c= sprintf(<<<__
/* Auto-generated from %s on %s by %s */
#include <php_embed.h>
#include "errorcb.h"

int main(int argc, char **argv)
{
  char *php_code = "%s";

  PHP_EMBED_START_BLOCK(argc, argv);
  zend_error_cb= _error_cb;
  if (FAILURE == zend_eval_string(php_code, NULL, "%s" TSRMLS_CC)) {
    fprintf(stderr, "*** Internal error\\n");
  }
  PHP_EMBED_END_BLOCK();

  return 0;
}

__
  , 
    $script,
    date('r'),
    get_current_user(),
    strtr(substr($src, 5, -3), array('"' => '\"')), 
    substr($script, 0, strrpos($script, '.php'))
  );
  
  echo $c;
  // }}}
?>

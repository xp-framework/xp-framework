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

static void _error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
  char *buffer;
  int buffer_len;
  
  if (!(EG(error_reporting) & type)) return;
  
  buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
  fprintf(stderr, "*** Error #%%d on line %%d of %%s\\n    %%s\\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);
  efree(buffer);
}

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

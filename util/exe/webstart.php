<?php
/* Create webstart executables
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'peer.http.HttpConnection', 
    'peer.URL',
    'util.cmd.ParamString'
  );

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s http://location/ scriptname.php > scriptname.c\n", basename($p->value(0)));
    exit();
  }
  
  $message= '';
  $location= &new URL($p->value(1).$p->value(2));  
  $conn= &new HttpConnection($location);
  try(); {
    if (($r= &$conn->head()) && (200 != ($sc= $r->getStatusCode()))) {
      throw(new IllegalArgumentException('Retreiving '.$location->getURL().' results in '.$sc));
    }
  } if (catch('Exception', $e)) {
    $message= '#warning "'.$e->message.'"';
  }
  
  
  $c= sprintf(<<<__
/* Auto-generated for %s on %s by %s */
#include <php_embed.h>
%s

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
  char *php_code;
  php_stream *stream;
  int len;

  PHP_EMBED_START_BLOCK(argc, argv);
  zend_error_cb= _error_cb;
  
  stream= php_stream_open_wrapper("%s", "rb", REPORT_ERRORS, NULL);
  if (!stream) {
    fprintf(stderr, "*** Failed to initiate\\n");
    return 255;
  }
  if ((len = php_stream_copy_to_mem(stream, &php_code, PHP_STREAM_COPY_ALL, 0)) >= 0) {
    if (FAILURE == zend_eval_string(php_code, NULL, "%s" TSRMLS_CC)) {
      fprintf(stderr, "*** Internal error\\n");
    }
  } else {
    fprintf(stderr, "*** Failed to retreive\\n");
  }
  php_stream_close(stream);
  
  PHP_EMBED_END_BLOCK();

  return 0;
}

__
  , 
    $location->getURL(),
    date('r'),
    get_current_user(),
    $message,
    $location->getURL(),
    $location->getURL()
  );
  
  echo $c;
  // }}}
?>

/* Error callback for executables
 *
 * $Id$ 
 */

static void _error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
  char *buffer;
  int buffer_len;
  
  if (!(EG(error_reporting) & type)) return;
  
  buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
  fprintf(stderr, "*** Error #%d on line %d of %s\n    %s\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);
  efree(buffer);
}

#include <stdlib.h>
#include <stdarg.h>
#include <stdio.h>

void print_error (char *file, int line, char *format, ...) {
	char *p;
	va_list ap;
	
	if ((p= malloc (1024)) == NULL) return;
	
	va_start (ap, format);
	(void)vsnprintf (p, 1024, format, ap);
	va_end (ap);
	
	fprintf (stderr, "[E] %s\n    at %s, line %d\n",
		p,
		file,
		line
	);
}

void log (char *file, int line, char *format, ...) {
	char *p;
	va_list ap;
	
	if ((p= malloc (1024)) == NULL) return;
	
	va_start (ap, format);
	(void)vsnprintf (p, 1024, format, ap);
	va_end (ap);
	
	fprintf (stdout, "[LOG] %s\n      at %s, line %d\n",
		p,
		file,
		line
	);

}

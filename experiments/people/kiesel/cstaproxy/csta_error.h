extern void print_error (char*, int, char*, ...);
extern void log (char*, int, char*, ...);

#define ERR(s)		print_error (__FILE__, __LINE__, s)
#define LOG(s)          log (__FILE__, __LINE__, s)

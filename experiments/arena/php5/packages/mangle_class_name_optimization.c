/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

#include <string.h>
#include <stdio.h>

char *active_package_entry_name = NULL;
int active_package_entry_length = 0;

#define MANGLE_MAIN_LEN sizeof("main~") - 1
  
inline static void mangle_class_name(char **class_name, int *name_len) {
  if (memcmp(*class_name, "main~", MANGLE_MAIN_LEN) == 0) {
    char *tmp = *class_name;
    
    *name_len -= MANGLE_MAIN_LEN;
    strncpy(tmp, tmp + MANGLE_MAIN_LEN, *name_len);
    tmp[*name_len] = 0;
  } else if (active_package_entry_name && !memchr(*class_name, '~', *name_len)) {
    int len = active_package_entry_length + 1 + *name_len;
    char *tmp = (char*) malloc(len);

    memcpy(tmp, active_package_entry_name, active_package_entry_length);
    memcpy(tmp + active_package_entry_length, "~", 1);
    memcpy(tmp + active_package_entry_length + 1, *class_name, *name_len);
    tmp[len] = 0;

    free(*class_name);
	*class_name = tmp;
    *name_len = len;
  }
}

int main(int argc, char** argv) { 
  char *class_name = NULL;
  int l;

  switch (argc) {
    case 2: break;
    case 3:
      active_package_entry_name = argv[2];
      active_package_entry_length = strlen(argv[2]);
      break;
    default:
      printf("Usage: %s <classname> [<packagename>]\n", argv[0]);
      return 1;
  }
  
  class_name = strdup(argv[1]);
  l = strlen(class_name);
  
  printf("Original: '%s' [%d]\n", class_name, l);
  mangle_class_name(&class_name, &l);
  printf("Mangled: '%s' [%d]\n", class_name, l);
  
  free(class_name);
  
  return 1;
}

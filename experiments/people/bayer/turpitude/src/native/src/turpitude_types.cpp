#include <Turpitude.h>

void turpitude_print_java_type(turpitude_java_type t) {
    char* typestring;
    switch (t) {
        case JAVA_UNKNOWN        : typestring = "JAVA_UNKNOWN"; break;
        case JAVA_VOID           : typestring = "JAVA_VOID"; break;
        case JAVA_OBJECT         : typestring = "JAVA_OBJECT"; break;
        case JAVA_BOOLEAN        : typestring = "JAVA_BOOLEAN"; break;
        case JAVA_BYTE           : typestring = "JAVA_BYTE"; break;
        case JAVA_CHAR           : typestring = "JAVA_CHAR"; break;
        case JAVA_SHORT          : typestring = "JAVA_SHORT"; break;
        case JAVA_INT            : typestring = "JAVA_INT"; break;
        case JAVA_LONG           : typestring = "JAVA_LONG"; break;
        case JAVA_FLOAT          : typestring = "JAVA_FLOAT"; break;
        case JAVA_DOUBLE         : typestring = "JAVA_DOUBLE"; break;
        case JAVA_ARRAY          : typestring = "JAVA_ARRAY"; break;
        case JAVA_OBJECT_ARRAY   : typestring = "JAVA_OBJECT_ARRAY"; break;
        case JAVA_BOOLEAN_ARRAY  : typestring = "JAVA_BOOLEAN_ARRAY"; break;
        case JAVA_BYTE_ARRAY     : typestring = "JAVA_BYTE_ARRAY"; break;
        case JAVA_CHAR_ARRAY     : typestring = "JAVA_CHAR_ARRAY"; break;
        case JAVA_SHORT_ARRAY    : typestring = "JAVA_SHORT_ARRAY"; break;
        case JAVA_INT_ARRAY      : typestring = "JAVA_INT_ARRAY"; break;
        case JAVA_LONG_ARRAY     : typestring = "JAVA_LONG_ARRAY"; break;
        case JAVA_FLOAT_ARRAY    : typestring = "JAVA_FLOAT_ARRAY"; break;
        case JAVA_DOUBLE_ARRAY   : typestring = "JAVA_DOUBLE_ARRAY"; break;
    }

    printf("Java Type: %5d, %s\n", t, typestring);
}

turpitude_java_type get_java_field_type(const char* sp) {
    turpitude_java_type retval = JAVA_UNKNOWN;

    // convert const char* to char* so we can iterate on the string
    // I will not alter the string, no, honestly...
    char* ptr = (char*)sp;

    // check if field is an array
    // if it actually is an array, set array bit
    if (*ptr == '[') {
        retval = JAVA_ARRAY;
        ptr++;
    }

    // check for other type besides array
    char c = *ptr;
    switch (c) {
        case 'Z': retval = (turpitude_java_type)(retval | JAVA_BOOLEAN);  break;  
        case 'B': retval = (turpitude_java_type)(retval | JAVA_BYTE);     break;  
        case 'C': retval = (turpitude_java_type)(retval | JAVA_CHAR);     break;  
        case 'S': retval = (turpitude_java_type)(retval | JAVA_SHORT);    break;  
        case 'I': retval = (turpitude_java_type)(retval | JAVA_INT);      break;  
        case 'J': retval = (turpitude_java_type)(retval | JAVA_LONG);     break;  
        case 'F': retval = (turpitude_java_type)(retval | JAVA_FLOAT);    break;  
        case 'D': retval = (turpitude_java_type)(retval | JAVA_DOUBLE);   break;  
        case 'V': retval = (turpitude_java_type)(retval | JAVA_VOID);     break;
        case 'L': retval = (turpitude_java_type)(retval | JAVA_OBJECT);   break;
        default:
        // none of the above - throw an error
        php_error(E_ERROR, "unable to determine method return type.");
    }
 
    return retval;
}


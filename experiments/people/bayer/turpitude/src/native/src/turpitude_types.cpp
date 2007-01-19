#include <Turpitude.h>


turpitude_java_type get_java_field_type(const char* sp) {
    turpitude_java_type retval = JAVA_UNKNOWN;

    char c = *sp;

    switch (c) {
        case 'Z': retval = JAVA_BOOLEAN;  break;  
        case 'B': retval = JAVA_BYTE;     break;  
        case 'C': retval = JAVA_CHAR;     break;  
        case 'S': retval = JAVA_SHORT;    break;  
        case 'I': retval = JAVA_INT;      break;  
        case 'J': retval = JAVA_LONG;     break;  
        case 'F': retval = JAVA_FLOAT;    break;  
        case 'D': retval = JAVA_DOUBLE;   break;  
        case 'V': retval = JAVA_VOID;     break;
        case 'L': retval = JAVA_OBJECT;   break;
        default:
        // none of the above - throw an error
        php_error(E_ERROR, "unable to determine method return type.");
    }

    return retval;
}


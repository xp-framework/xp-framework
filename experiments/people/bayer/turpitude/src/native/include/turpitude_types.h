#ifndef __TURPITUDE_TYPES_H__
#define __TURPITUDE_TYPES_H__

enum turpitude_java_type {
    JAVA_VOID       = 0,
    JAVA_OBJECT     = 1,
    JAVA_BOOLEAN    = 2,
    JAVA_BYTE       = 3,
    JAVA_CHAR       = 4,
    JAVA_SHORT      = 5,
    JAVA_INT        = 6,
    JAVA_LONG       = 7,
    JAVA_FLOAT      = 8,
    JAVA_DOUBLE     = 9,
    JAVA_UNKNOWN    = 666
};

/** returns the turpitude_java_type from a JNI field signature */
turpitude_java_type get_java_field_type(const char* sig);

#endif

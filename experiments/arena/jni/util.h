/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>

static void throw(JNIEnv* env, const char *classname, const char* message) {
    jclass exception = (*env)->FindClass(env, classname);
    if (exception != 0) {
        (*env)->ThrowNew(env, exception, message);
    }
}

/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

public class CompiledScript {

    /**
     * Calls a piece of PHP sourcecode
     *
     */
    public native Object call(Object self, String methodName, Object[] args);

}

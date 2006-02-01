/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

public class CompiledScript {
    private transient java.nio.ByteBuffer oparrayptr;
    
    /**
     * Ensures this class cannot be instantiated from userland. Rather,
     * call PHPExecutor.compile() to retrieve an instance.
     *
     */
    private CompiledScript() { }

    /**
     * Calls a piece of PHP sourcecode
     *
     */
    public native Object call(Object self, String methodName, Object[] args);

}

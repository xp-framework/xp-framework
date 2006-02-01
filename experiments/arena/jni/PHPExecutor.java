/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

import static java.lang.System.out;

public class PHPExecutor {

    static {
        System.loadLibrary("php");
        startUp();
        Runtime.getRuntime().addShutdownHook(new Thread() {
            public void run() {
                shutDown();
            }
        });
    }

    /**
     * Starts up a the PHP engine. Called from static initializer
     *
     */
    protected static native void startUp();

    /**
     * Shuts down the PHP engine. Called from finalizer.
     *
     */
    protected static native void shutDown();

    /**
     * Compiles a piece of PHP sourcecode
     *
     */
    public native CompiledScript compile(String s);

    /**
     * Evaluates a piece of PHP sourcecode
     *
     */
    public native Object eval(String s);
    
    public static void usage() {
        out.println("Usage: PHPExecutor <method> [arguments]");
    }
    
    public static void main(String[] args) throws Exception {
        if (args.length < 2) {
            usage();
            return;
        }

        PHPExecutor executor= new PHPExecutor();
        
        // Switch on argument
        if ("eval".equals(args[0])) {
            executor.eval(args[1]);
        } else if ("compile".equals(args[0])) {
            /*
            CompiledScript compiled= executor.compile(args[1]);
            compiled.call(null, args[2], new Object[] { });
            */
            
            CompiledScript compiled= executor.compile(
                "class Foo { function a() { var_dump($this, 'Foo::a();'); } } " +
                "function create($class) { $GLOBALS[$class]= new $class(); } " +
                "function invoke($class, $method) { call_user_func(array($GLOBALS[$class], $method)); }"
            );
            compiled.call(null, "create", new Object[] { "Foo" });
            compiled.call(null, "invoke", new Object[] { "Foo", "a" });
            compiled.call(null, "invoke", new Object[] { "Foo", "b" });
        }
        
        out.println("===> Done");
    }
}

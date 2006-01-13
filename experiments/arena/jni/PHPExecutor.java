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
     * Evaluates a piece of PHP sourcecode
     *
     */
    public native Object eval(String s);
    
    public static void main(String[] args) throws Exception {
        PHPExecutor executor= new PHPExecutor();
        
        for (int i= 0; i < args.length; i++) {
            System.out.println("============ Executing #" + i + " ============");
            executor.eval(args[i]);
        }
        
        System.out.println("===> Done, executed " + args.length + " snippets");
    }
}

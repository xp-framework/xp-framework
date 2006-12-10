
package net.xp_framework.turpitude;

import javax.script.*;
import java.io.Reader;
import java.io.IOException;

public class PHPScriptEngine extends AbstractScriptEngine {

    private ScriptEngineFactory MyFactory = null; //my factory, may be null

    /**
     * Constructor
     */
    PHPScriptEngine(ScriptEngineFactory fac) {
        setScriptEngineFactory(fac);
    }

    /**
     * Executes a script from a Reader containing the source code
     * @return The value returned by the script
     */
    public Object eval(Reader reader, ScriptContext ctx) throws ScriptException,
                                                                NullPointerException {
        //check reader validity
        if (reader == null)
            throw new NullPointerException("reader == null");
        try {
            if (!reader.ready())
                throw new ScriptException("reader not ready");
        } catch (IOException exp) {
            throw new ScriptException(exp);
        }

        // extract String from reader
        char[] arr = new char[8*1024]; // 8K at a time
        StringBuffer buf = new StringBuffer();
        int numChars;
        try {
            while ((numChars = reader.read(arr, 0, arr.length)) > 0) 
                buf.append(arr, 0, numChars);
        } catch (IOException exp) {
            throw new ScriptException(exp);
        }

        return eval(buf.toString(), ctx);
    }

    /**
     * Executes a script from a string containing the source code
     * @return The value returned by the script
     */
    public Object eval(String str, ScriptContext ctx) throws ScriptException,
                                                             NullPointerException {
        //TODO: implement
        return null;
    }

    /**
     * @return an uninitialized Bindings
     * uses SimpleBindings
     */
    public Bindings createBindings() {
        return new SimpleBindings();
    }   

    /**
     * @return a ScriptEngineFactory for the class to which this ScriptEngine belongs.
     */
    public ScriptEngineFactory getFactory() {
        //create factory if it does not exist, threadsafe
        synchronized (this) {
            if (MyFactory == null)
                MyFactory = new PHPScriptEngineFactory();
        }
        return MyFactory;
    }

    /**
     * sets the factory
     */
    void setScriptEngineFactory(ScriptEngineFactory fac) {
        MyFactory = fac;
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

}

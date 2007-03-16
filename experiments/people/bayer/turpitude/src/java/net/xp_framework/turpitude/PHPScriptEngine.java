
package net.xp_framework.turpitude;

import javax.script.*;
import java.io.Reader;
import java.io.IOException;
import java.util.Properties;
import java.util.Set;
import java.util.Iterator; 

public class PHPScriptEngine extends AbstractScriptEngine implements Compilable, Invocable {

    private ScriptEngineFactory MyFactory = null; //my factory, may be null
    private String TurpitudeVarName = "TURP_ENV"; //turpitude variable name
    private PHPCompiledScript lastScript = null; //last compiled script
    public static final String PropertyPrefix = "net.xp_framework.turpitude.ini";

    /**
     * Constructor
     */
    PHPScriptEngine(ScriptEngineFactory fac) {
        setScriptEngineFactory(fac);
        //load library
        try {
            System.loadLibrary("turpitude");
        } catch(UnsatisfiedLinkError e) {
            System.out.println("library load failed");
            throw(e);
        }
        startUp();
        //make sure we shut down properly, at least whenever the vm shuts down
        Runtime.getRuntime().addShutdownHook(new Thread() {
                public void run() {
                    shutDown();
                }
        });
    }

    /**
     * reads the php.ini parameters from the system properties and sets them using 
     * zend_alter_ini_entry
     */
    protected void setIniParams() {
        Properties props = System.getProperties();
        Set<String> keys = props.stringPropertyNames();
        Iterator<String> it = keys.iterator();
        while (it.hasNext()) {
            String str = it.next();
            if (str.startsWith(PropertyPrefix)) {
                // setting php ini parameters
                String phpkey = str.substring(PropertyPrefix.length()+1);
                String val = props.getProperty(str);
                setIniParam(phpkey, val);
            }
        }
    }

    /**
     * finalize method to shut down script engine whenever this objects
     * is destroyed
     */
    protected void finalize() throws Throwable {
        try {
            shutDown();
        } finally {
            super.finalize();
        }
    }
   
    /**
     * set the turpitude variable name
     * the turpitude context inside the script will be accessible
     * via this name.
     */
    public void setVarName(String newname) {
        TurpitudeVarName = newname;
    }

    /**
     * get the turpitude variable name
     * the turpitude context inside the script will be accessible
     * via this name.
     */
    public String getVarName() {
        return TurpitudeVarName;
    }

    /**
     * helper method, extracts a String from a reader
     */
    private static String read(Reader reader) throws ScriptException,
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

        return buf.toString();
    }

    /**
     * parses the php tags from a string
     * e.g. &lt;?php
     * also prepends some necessary code
     */
    private String preparePHPCode(String in) {
        String out = "";

        if (in.substring(0, 5).equals("<?php")) {
            out = in.substring(5);
        } else if (in.substring(0, 2).equals("<?")) {
            out = in.substring(2);
        } else {
            out = in;
        }
        StringBuffer sb = new StringBuffer();
        sb.append("$_SERVER[\"");
        sb.append(getVarName());
        sb.append("\"] = new TurpitudeEnvironment();");
        sb.append(out);

        return sb.toString();
    }

    /**
     * Executes a script from a Reader containing the source code
     * @return The value returned by the script
     */
    public Object eval(Reader reader, ScriptContext ctx) throws ScriptException,
                                                                NullPointerException {
        return eval(read(reader), ctx);
    }

    /**
     * Executes a script from a string containing the source code
     * @return The value returned by the script
     */
    public Object eval(String str, ScriptContext ctx) throws ScriptException,
                                                             NullPointerException {
        CompiledScript sc = compile(str);
        return sc.eval(ctx);
    }

    /**
     * Compiles the script (source represented as a String) for later execution.
     */
    public CompiledScript compile(Reader script) throws ScriptException,
                                                        NullPointerException {
        return compile(read(script));
    }

    /**
     * Compiles the script (source contained in a Reader) for later execution.
     */
     public CompiledScript compile(String script) throws ScriptException,
                                                         NullPointerException {
        script = preparePHPCode(script);
        Object o = compilePHP(script);
        if (!(o instanceof PHPCompiledScript)) {
            throw (new ScriptException("compile did not return a CompiledScript" + o));
        }
        PHPCompiledScript ret = (PHPCompiledScript)o;
        ret.setEngine(this);
        lastScript = ret;
        return (CompiledScript)o;
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
     * @see javax.script.Invocable
     */
    public Object invokeMethod(Object thiz, String name, Object... args) throws IllegalArgumentException,
                                                                                ScriptException,
                                                                                NullPointerException,
                                                                                NoSuchMethodException {
        if (lastScript == null)
            throw new NullPointerException("no previously compiled script found");
        return lastScript.invokeMethod(thiz, name, args);
    }

    /**
     * @see javax.script.Invocable
     */
    public Object invokeFunction(String name, Object... args) throws ScriptException,
                                                                     NullPointerException,
                                                                     NoSuchMethodException {
        if (lastScript == null)
            throw new NullPointerException("no previously compiled script found");
        return lastScript.invokeFunction(name, args);
    }

    /**
     * @see javax.script.Invocable
     */
    public <T> T getInterface(Object thiz, Class<T> clasz) throws IllegalArgumentException {
        if (lastScript == null)
            throw new IllegalArgumentException("no previously compiled script found");
        return lastScript.getInterface(thiz, clasz);
    }

    /**
     * @see javax.script.Invocable
     */
    public <T> T getInterface(Class<T> clasz) throws IllegalArgumentException {
        if (lastScript == null)
            throw new IllegalArgumentException("no previously compiled script found");
        return lastScript.getInterface(clasz);
    }

    /**
     * Starts up a the PHP engine. Called from Constructor
     */
    protected native void startUp();

    /**
     * Shuts down the PHP engine. Called from finalizer.
     */
    protected native void shutDown();

    /**
     * calls native php interpreter to eval the sourcecode
     */
    protected native Object compilePHP(String source);

    /**
     * sets a php.ini parameter to the given value, only has an effect if the engine
     * was already initialized. Called by setIniParams
     */
    protected native void setIniParam(String key, String val);

}

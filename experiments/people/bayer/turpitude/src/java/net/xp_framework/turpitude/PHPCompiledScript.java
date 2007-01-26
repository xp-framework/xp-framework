package net.xp_framework.turpitude;

import javax.script.*;
import java.nio.ByteBuffer;

public class PHPCompiledScript extends CompiledScript implements Invocable {
   
    private ScriptEngine MyEngine = null;
    private transient java.nio.ByteBuffer ZendOpArrayptr;

    /**
     * protected constructor to prevent users from
     * creating instances directly
     */
    protected PHPCompiledScript(ScriptEngine eng) {
        MyEngine = eng;
    }

    /**
     * return the ScriptEngine that created this object
     */
    public ScriptEngine getEngine() {
        return MyEngine;
    }

    /**
     * set the ScriptEngine that created this object
     */
    ScriptEngine setEngine(ScriptEngine eng) {
        return MyEngine = eng;
    }

    /**
     * executes this script
     */
    public Object eval(ScriptContext context) {
        return execute(context);
    }

    /**
     * @see javax.script.Invocable
     */
    public Object invokeMethod(Object thiz, String name, Object... args) {
        // check parameter validity
        if (thiz == null || !(thiz instanceof PHPObject))
            throw new IllegalArgumentException("invalid value for parameter thiz: " + thiz);
        
        return nativeInvokeMethod(thiz, name, args);
    }

    /**
     * @see javax.script.Invocable
     */
    public Object invokeFunction(String name, Object... args) {
        return nativeInvokeFunction(name, args);
    }

    /**
     * @see javax.script.Invocable
     */
    public <T> T getInterface(Object thiz, Class<T> clasz) {
        return null;
    }

    /**
     * @see javax.script.Invocable
     */
    public <T> T getInterface(Class<T> clasz) {
        return null;
    }

    /**
     * native mehtod, called by eval
     */
    private native Object execute(ScriptContext cts);

    /**
     * native mthod, called by invokeFunction
     */
    private native Object nativeInvokeFunction(String name, Object... args);

    /**
     * native mthod, called by invokeMethod
     */
    private native Object nativeInvokeMethod(Object thiz, String name, Object... args);

}

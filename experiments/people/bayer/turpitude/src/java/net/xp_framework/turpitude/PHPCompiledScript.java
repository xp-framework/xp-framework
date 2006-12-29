package net.xp_framework.turpitude;

import javax.script.*;
import java.nio.ByteBuffer;

public class PHPCompiledScript extends CompiledScript {
   
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
     * native mehtod, called by eval
     */
    private native Object execute(ScriptContext cts);


}

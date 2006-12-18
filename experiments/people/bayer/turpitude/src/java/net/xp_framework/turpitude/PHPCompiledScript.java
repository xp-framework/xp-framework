package net.xp_framework.turpitude;

import javax.script.*;

public class PHPCompiledScript extends CompiledScript {
   
    private ScriptEngine MyEngine = null;

    /**
     * protected constructor to prevent users from
     * creating instances directly
     */
    public PHPCompiledScript(ScriptEngine eng) {
        MyEngine = eng;
    }

    /**
     * return the ScriptEngine that created this object
     */
    public ScriptEngine getEngine() {
        return MyEngine;
    }

    public Object eval(ScriptContext context) {
        // TODO: implement;
        return null;
    }

}

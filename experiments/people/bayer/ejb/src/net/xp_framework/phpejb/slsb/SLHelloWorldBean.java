package net.xp_framework.phpejb.slsb;

import javax.ejb.Stateless;
import javax.script.*;
import java.util.List;
import java.util.ArrayList;
import java.util.ListIterator;
import javax.script.*;

@Stateless
public class SLHelloWorldBean implements SLHelloWorld {

    public String sayHello(String s) throws ScriptException {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng)
            throw new ScriptException("script engine not found");

        SLHelloWorld hw = null;
        try {
            Compilable comp = (Compilable)eng;
            CompiledScript script = comp.compile(getSource());
            Invocable inv = (Invocable)script;
            hw = inv.getInterface(SLHelloWorld.class);
        } catch(Throwable e) {
            e.printStackTrace();
            throw new ScriptException(e.getMessage());
        }

        return hw.sayHello(s);
    }

    private static String getSource() {
        StringBuffer src = new StringBuffer();
        src.append("<?php");
        src.append("class SLHelloWorld {");
        src.append("  function sayHello($s) {");
        src.append("    return 'The PHP-implementation says hello to '.$s;");
        src.append("  }");
        src.append("}");
        src.append("?>");
        return src.toString();
    }

}


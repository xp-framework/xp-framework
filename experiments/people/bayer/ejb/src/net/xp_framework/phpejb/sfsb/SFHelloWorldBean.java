package net.xp_framework.phpejb.sfsb;

import javax.ejb.Stateful;
import javax.ejb.PrePassivate;
import javax.ejb.PostActivate;
import javax.annotation.PostConstruct;
import javax.script.*;
import java.io.Serializable;
import net.xp_framework.turpitude.PHPObject;

@Stateful
public class SFHelloWorldBean implements SFHelloWorld {
    private PHPObject MyObject = null;
    private Invocable MyInvocable = null;
    private String serialized = null;
    

    @PostConstruct
    public void initialize() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng) {
            System.out.println("script engine not found");
            return;
        }

        try {
            Compilable comp = (Compilable)eng;
            CompiledScript script = comp.compile(getSource());
            MyInvocable = (Invocable)script;
            Object phpobj = MyInvocable.invokeFunction("getInstance");
            MyObject = PHPObject.class.cast(phpobj);
        } catch(Throwable e) {
            //suppress exceptions
            e.printStackTrace();
        }
    }

    @PrePassivate
    public void sleep() {
        try {
            serialized = String.class.cast(MyInvocable.invokeMethod(MyObject, "ser"));
        } catch(Throwable e) {
            //suppress exceptions
            e.printStackTrace();
        }
        MyObject = null;
        MyInvocable = null;
    }
    
    @PostActivate
    public void wakeUp() {
        initialize();
        try {
            MyInvocable.invokeMethod(MyObject, "unser", serialized);
        } catch(Throwable e) {
            //suppress exceptions
            e.printStackTrace();
        }
    }
    
    public void setName(String s) throws ScriptException {
        SFHelloWorld hw = MyInvocable.getInterface(MyObject, SFHelloWorld.class); 
        hw.setName(s);
    }

    public String sayHello() throws ScriptException {
        SFHelloWorld hw = MyInvocable.getInterface(MyObject, SFHelloWorld.class);
        return hw.sayHello();
    }

    private static String getSource() {
        StringBuffer src = new StringBuffer();
        src.append("<?php");
        src.append("class SFHelloWorld {");
        src.append("  var $name = 'initial value';");
        src.append("  function setName($n) {");
        src.append("    $this->name = $n;");
        src.append("  }");
        src.append("  function sayHello() {");
        src.append("    return 'Stateful PHP says hello to '.$this->name;");
        src.append("  }");
        src.append("  function ser($obj) {");
        src.append("    return serialize($obj);");
        src.append("  }");
        src.append("  function unser($str) {");
        src.append("    $cpy = unserialize($str);");
        src.append("    $this->setName($cpy->name);");
        src.append("  }");
        src.append("}");
        src.append("function getInstance() {");
        src.append("  return new SFHelloWorld();");
        src.append("}");
        src.append("?>");
        return src.toString();
    }

}


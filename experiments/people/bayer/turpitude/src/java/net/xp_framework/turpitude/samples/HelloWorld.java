package net.xp_framework.turpitude.samples;

import javax.script.*;

public class HelloWorld {

   /**
    * default constructor
    */
    public HelloWorld() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng) {
            System.out.println("unable to find engine, please check classpath");
            return;
        }
        System.out.println("found Engine: " + eng.getFactory().getEngineName());
        try {
            eng.eval("echo(\"Hello World!\n\");");
        } catch(ScriptException e) {
            System.out.println("ScriptException caught:");
            e.printStackTrace();
            return;
        }
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        HelloWorld hw = new HelloWorld();
    }
 

}


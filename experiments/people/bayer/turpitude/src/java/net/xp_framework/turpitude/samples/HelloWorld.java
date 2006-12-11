package net.xp_framework.turpitude.samples;

import javax.script.*;

public class HelloWorld {

   /**
    * default constructor
    * outputs a list of available ScriptEngines via System.out
    */
    public HelloWorld() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude"); 
        System.out.println("found Engine: " + eng.getFactory().getEngineName());
        try {
            eng.eval("<?php echo \"Hello World!\"; ?>");
        } catch(ScriptException e) {
            System.out.println("Exception while executing PHP code");
            e.printStackTrace();
        }
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        HelloWorld el = new HelloWorld();
    }
 

}


package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ContextSample {

   /**
    * default constructor
    */
    public ContextSample() {
    }

    /**
     * executes a script from a file
     */
    public void exec() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng) {
            System.out.println("unable to find engine, please check classpath");
            return;
        }
        System.out.println("found Engine: " + eng.getFactory().getEngineName());
        ScriptContext ctx = eng.getContext();
        ctx.setAttribute("string", "stringval", ScriptContext.ENGINE_SCOPE);

        Object retval;
        try {
            retval = eng.eval(getSource());
        } catch(PHPCompileException e) {
            System.out.println("Compile Error:");
            e.printStackTrace();
            return;
        } catch(PHPEvalException e) {
            System.out.println("Eval Error:");
            e.printStackTrace();
            return;
        } catch(ScriptException e) {
            System.out.println("ScriptException caught:");
            e.printStackTrace();
            return;
        }
        if (null == retval)
            System.out.println("done evaluating, return value " + retval);
        else 
            System.out.println("done evaluating, return value " + retval.getClass() + " : " + retval);
    }

    private static String getSource() {
        StringBuffer src = new StringBuffer();
        src.append("<?php \n");
        //src.append("var_dump($_SERVER);\n");
        src.append("$turpenv = $_SERVER[\"TURP_ENV\"]; \n");
        //src.append("var_dump($turpenv);\n");
        //src.append("$turpenv->lala();\n");
        src.append("$class = $turpenv->findClass(\"java/util/Date\");\n");
        src.append("var_dump($class);");
        src.append("$constructor = $class->findConstructor('()V');");
        src.append("var_dump($constructor);");
        src.append("$instance = $class->create($constructor, 1168792209);");
        src.append("var_dump($instance);");
        src.append("?>"); 
        return src.toString();
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ContextSample cs = new ContextSample();
        cs.exec();
    }
 

}


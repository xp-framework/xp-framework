package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ExceptionSample {

   /**
    * default constructor
    */
    public ExceptionSample() {
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
        src.append("$turpenv = $_SERVER[\"TURP_ENV\"]; \n");
        src.append("echo \"before\n\";");
        src.append("$class = $turpenv->findClass('java/lang/Exception');");
        src.append("$constructor = $class->findConstructor('(Ljava/lang/String;)V');");
        src.append("$instance = $class->create($constructor, 'Test');");
        src.append("$turpenv->throw($instance);");
        //src.append("$turpenv->throwNew('java/lang/IllegalArgumentException', 'Test');");
        //src.append("$turpenv->throwNew('non/existent/Exception', 'Test');");
        src.append("echo \"after\n\";");
        src.append("?>"); 
        return src.toString();
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ExceptionSample cs = new ExceptionSample();
        cs.exec();
    }
 

}


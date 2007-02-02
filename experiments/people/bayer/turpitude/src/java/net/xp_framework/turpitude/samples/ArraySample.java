package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ArraySample {

   /**
    * default constructor
    */
    public ArraySample() {
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
        src.append("$class = $turpenv->findClass(\"net/xp_framework/turpitude/samples/ExampleClass\");\n");
        src.append("$constr = $class->findConstructor('()V');");
        src.append("$instance = $class->create($constr);");
        src.append("$method = $class->findMethod('getIntArray', '()[I');");
        src.append("$retval = $instance->javaInvoke($method);");
        src.append("$len = $retval->getLength();");
        src.append("var_dump($len);");
        src.append("$val = $retval->get(2);");
        src.append("var_dump($val);");
        src.append("$method = $class->findMethod('getStringArray', '()[Ljava/lang/String;');");
        src.append("$retval = $instance->javaInvoke($method);");
        src.append("$len = $retval->getLength();");
        src.append("var_dump($len);");
        src.append("$val = $retval->get(2);");
        src.append("var_dump($val);");
        src.append("?>"); 
        return src.toString();
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ArraySample cs = new ArraySample();
        cs.exec();
    }
 

}


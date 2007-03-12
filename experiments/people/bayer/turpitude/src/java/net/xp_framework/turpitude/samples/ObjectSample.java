package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ObjectSample {

   /**
    * default constructor
    */
    public ObjectSample() {
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
        src.append("$class = $turpenv->findClass(\"net/xp_framework/turpitude/samples/ExampleClass\");\n");
        src.append("$method = $class->findStaticMethod('staticMethod', '(I)Ljava/lang/String;');");
        src.append("$retval = $class->invokeStatic($method, 17);");
        src.append("var_dump($retval);");
        src.append("$class->setStatic('staticInt', 'I', 13);");
        src.append("$retval = $class->getStatic('staticInt', 'I');");
        src.append("var_dump($retval);");
        src.append("$constructor = $class->findConstructor('(ILjava/lang/String;)V');");
        src.append("$instance = $class->create($constructor, 1337, 'eleet');");
        src.append("$str = $instance->javaGet('stringval', 'Ljava/lang/String;');");
        src.append("var_dump($str);");
        src.append("$instance->javaSet('intval', 'I', 666);");
        src.append("$int = $instance->javaGet('intval', 'I');");
        src.append("var_dump($int);");
        src.append("$instance->javaSet('privateString', 'Ljava/lang/String;', 'touched it!');");
        src.append("$priv = $instance->javaGet('privateString', 'Ljava/lang/String;');");
        src.append("var_dump($priv);");
        src.append("$method = $class->findMethod('setValues', '(ILjava/lang/String;)V');");
        src.append("$instance->javaInvoke($method, 1338, 'eleeter');");
        src.append("$method = $class->findMethod('toString', '()Ljava/lang/String;');");
        src.append("$result = $instance->javaInvoke($method);");
        src.append("echo $result.\"\\n\";");
        src.append("$result = $instance->getDate();");
        src.append("$io = $turpenv->instanceOf($instance, 'java/util/Date');");
        src.append("var_dump($io);");
        src.append("$io = $turpenv->instanceOf($instance, $class);");
        src.append("var_dump($io);");
        src.append("return $result;");
        src.append("?>"); 
        return src.toString();
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ObjectSample cs = new ObjectSample();
        cs.exec();
    }
 

}


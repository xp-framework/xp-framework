package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class CompileSample {

   /**
    * default constructor
    */
    public CompileSample() {
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
        if (!(eng instanceof Compilable)) {
            System.out.println("engine does not implement compilable...");
            return;
        }
        Compilable comp = (Compilable)eng;
        String src = "echo \"Compiled Script output\\n\"; ";

        try {
            System.out.println("compiling: " + src);
            CompiledScript script = comp.compile(src);
            for (int i=0; i<5; i++) {
                System.out.println("executing " + i);
                script.eval();
            }
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
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        CompileSample cs = new CompileSample();
        cs.exec();
    }
 

}


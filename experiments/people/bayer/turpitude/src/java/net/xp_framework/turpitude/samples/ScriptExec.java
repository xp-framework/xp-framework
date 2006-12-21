package net.xp_framework.turpitude.samples;

import javax.script.*;
import java.io.FileReader;
import java.io.FileNotFoundException;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ScriptExec {

   /**
    * default constructor
    */
    public ScriptExec() {
    }

    /**
     * executes a script from a file
     */
    public void exec(String filename) throws FileNotFoundException {
        System.out.println("reading file: " + filename);
        FileReader r = new FileReader(filename);

        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng) {
            System.out.println("unable to find engine, please check classpath");
            return;
        }
        System.out.println("found Engine: " + eng.getFactory().getEngineName());
        System.out.println("evaluating... ");
        Object retval = null;
        try {
            retval = eng.eval(r);
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

    public static void echoUsage() {
        System.out.println("please provide a filename");
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ScriptExec se = new ScriptExec();
        if (argv.length < 1) {
            echoUsage();
            System.exit(-1);
        }
        try {
            se.exec(argv[0]);
        } catch(FileNotFoundException e) {
            e.printStackTrace();
        }
    }
 

}


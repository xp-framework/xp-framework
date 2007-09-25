package net.xp_framework.cmd;

import java.io.PrintStream;
import java.lang.reflect.Method;
import java.lang.reflect.InvocationTargetException;
import java.io.File;
import javax.tools.*;
import java.util.List;
import java.util.Arrays;
import java.net.URL;
import java.net.URLClassLoader;

public class Runner {
    private static PrintStream out= System.out;
    private static PrintStream err= System.err;

    /**
     * Main method
     *
     * @param   args 
     */
    public static void main(String... args) {
        System.exit(new Runner().run(new ParamString(args)));
    }
    
    /**
     * Run
     *
     * @param   params ParamString object
     * @return  exitcode
     */
    public int run(ParamString params) {
        if (!params.exists(0)) {
            err.println("*** Missing classname");
            return 1;
        }
        
        // Figure out classname
        String classname= params.value(0);
        ParamString classparams= new ParamString(params.list.subList(1, params.count).toArray(new String[] { }));
        
        ClassLoader cl= null;

        // Compile .java files, load classes using the system class loader otherwise
        if (!classname.endsWith(".java")) {
            cl= ClassLoader.getSystemClassLoader();
        } else {
            File classfile= new File(classname);
            boolean success= false;

            try {
                JavaCompiler tool= ToolProvider.getSystemJavaCompiler();
                StandardJavaFileManager manager= tool.getStandardFileManager(null, null, null);

                success= tool.getTask(
                    null, 
                    manager, 
                    null, 
                    null, 
                    null, 
                    manager.getJavaFileObjectsFromFiles(Arrays.asList(classfile))
                ).call();
                manager.close();

                cl= new URLClassLoader(
                    new URL[] { classfile.getCanonicalFile().toURI().toURL() } 
                );
            } catch (Exception e) {
                err.println("*** Compilation failed");
                e.printStackTrace(err);
                return 1;
            }
            
            if (!success) {
                err.println("*** Compilation failed");
                return 1;
            }
            
            classname= classname.substring(0, classname.length() -5).replace("/", ".");
        }

        Class clazz= null;
        try {
            clazz= Class.forName(classname, true, cl);
        } catch (ClassNotFoundException e) {
            err.println("*** Class " + classname + " does not exist: " + e.getMessage());
            return 1;
        }
        
        if (clazz.isAssignableFrom(Runnable.class)) {
            err.println("*** " + clazz.getName() + " is not runnable");
            return 1;
        }
        
        // Usage
        if (classparams.exists("help", '?')) {
            err.printf("Usage: jcli %s%n", clazz.getName());
            return 0;
        }
        
        // Load, instantiate and initialize
        Command instance= null;
        try {
            instance= (Command)clazz.newInstance();
        } catch (InstantiationException e) {
            err.println("*** Could not instantiate: " + e.getMessage());
            return 1;
        } catch (IllegalAccessException e) {
            err.println("*** Could not instantiate: " + e.getMessage());
            return 1;
        }        
        
        instance.out= out;
        instance.err= err;
        
        for (Method m: clazz.getMethods()) {
            if (m.isAnnotationPresent(Arg.class)) {   // Pass arguments
                Arg a= m.getAnnotation(Arg.class);
                String longName;
                char shortName= 0;
                boolean exists;
                boolean positional= false;
                Object[] args= null;

                if (-1 != a.position()) {
                    longName= "#" + String.valueOf(a.position() + 1);
                    exists= classparams.exists(a.position());
                    positional= true;
                } else if (!("".equals(a.name()))) {
                    longName= a.name(); 
                    shortName= a.option() == 0 ? longName.charAt(0) : a.option();
                    exists= classparams.exists(longName);
                } else {
                    longName= m.getName().replaceFirst("^set", "").toLowerCase();
                    shortName= a.option() == 0 ? longName.charAt(0) : a.option();
                    exists= classparams.exists(longName);
                }
                
                if (0 == m.getParameterTypes().length) {
                    if (!exists) continue;
                    
                    args= new Object[] { };
                } else if (!exists) {
                    err.println("*** Argument " + longName + " does not exist!");
                    return 2;
                } else {
                    args= new Object[] { positional
                        ? classparams.value(a.position())
                        : classparams.value(longName, shortName) 
                    };
                }
                
                try {
                    m.invoke(instance, args);
                } catch (IllegalAccessException e) {
                    err.println("*** Could not invoke " + m);
                    return 2;
                } catch (InvocationTargetException e) {
                    err.println("*** " + e.getMessage() + " " + e.getCause());
                    return 2;
                }
            }
        }
        
        instance.run();
        
        return 0;
    }
}

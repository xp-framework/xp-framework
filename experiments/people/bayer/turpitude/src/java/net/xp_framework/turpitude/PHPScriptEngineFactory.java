package net.xp_framework.turpitude;

import javax.script.*;
import java.util.List;
import java.util.ArrayList;

public class PHPScriptEngineFactory implements ScriptEngineFactory {

    /**
     * @return the name of the ScriptEngine.
     */
    public String getEngineName() {
        return "XP-Framework Turpitude PHP Engine"; 
    }

    /**
     * @return the version of the ScriptEngine.
     */
    public String getEngineVersion() {
        return "0.1"; 
    }

    /**
     * @return a list of extensions this engine handles
     */
    public List<String> getExtensions() {
        List<String> exts = new ArrayList<String>();
        exts.add("php");
        exts.add("pp5");
        return exts;
    }

    /**
     * @return a list of mimetypes this engine handles
     */
    public List<String> getMimeTypes() {
        //TODO: automate version dependant mimetypes
        List<String> types = new ArrayList<String>();
        types.add("application/x-httpd-php");
        types.add("application/x-httpd-php5");
        types.add("application/x-httpd-php-source");
        types.add("application/x-httpd-php5-source");
        return types;
    }

    /**
     * @return a list of alternative names 
     */
    public List<String> getNames() {
        List<String> names = new ArrayList<String>();
        names.add("PHP5");
        names.add("PHP");
        return names;
    }
    
    /**
     * @return a String containing "PHP"
     */
    public String getLanguageName() {
        return "PHP";
    }

    /**
     * @return a String containing the PHP Version
     */
    public String getLanguageVersion() {
        //TODO: automate version identification
        return "5.0";
    }

    /**
     *
     */
    public Object getParameter(String key) {
        //TODO: implement
        return null;
    }

    /**
     * @return A String containing PHP code which can be used to invoke a method of a Java object
     *
     */
    public String getMethodCallSyntax(String obj, String m, String... args) {
        StringBuffer sb = new StringBuffer();

        //object name
        sb.append("$");
        sb.append(obj);

        //method call
        sb.append("->");
        sb.append(m);
        sb.append("(");

        //arguments
        for (int i = 0; i < args.length; i++) {
            sb.append(args[i]);
            if (i != args.length-1)
                sb.append(",");
        }

        //close parenthesis, add semicolon
        sb.append(");");

        return sb.toString();
    }

    /**
     * @return A String that can be used as a statement to display 
     *         the specified String using the syntax of PHP.
     */
    public String getOutputStatement(String toDisplay) {
        StringBuffer sb = new StringBuffer();
        
        sb.append("printf(\"%s\",\"");
        sb.append(toDisplay);
        sb.append("\");");
        return sb.toString();
    }

    /**
     * @return A valid PHP executable progam with given statements.
     */
    public String getProgram(String... statements) {
        StringBuffer sb = new StringBuffer();
        
        sb.append("<?php\n");
        for (int i = 0; i < statements.length; i++) {
            sb.append(statements[i]);
            sb.append(";\n");
        }
        sb.append("?>");
        return sb.toString();
    }

    /**
     * @return An instance of PHPScriptEngine
     */
    public ScriptEngine getScriptEngine() {
        //TODO: implement
        return null;
    }
}

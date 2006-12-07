package net.xp_framework.turpitude;

import javax.script.*;

public class PHPScriptEngineFactory implements ScriptEngineFactory {

    public String getMethodCallSyntax(String obj, String m, String... args) {
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

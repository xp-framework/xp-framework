/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import net.xp_framework.easc.reflect.MethodDescription;
import java.util.ArrayList;
import java.io.Serializable;

/**
 * Describes one of the interfaces of an EJB
 *
 */
public class InterfaceDescription implements Serializable {
    public String className;
    public ArrayList<MethodDescription> methods = new ArrayList<MethodDescription>();
    
    /**
     * Get class name of this bean
     *
     * @access  public
     * @return  java.lang.String
     */
    public String getClassName() {
        return this.className;
    }
    
    /**
     * Set class name of this bean
     *
     * @access  public
     * @param   java.lang.String name
     */
    public void setClassName(String className) {
        this.className= className;
    }

    /**
     * Add method
     *
     * @access  public
     * @param   net.xp_framework.beans.reflect.MethodDescription m
     * @return  net.xp_framework.beans.reflect.MethodDescription the added object
     */
    public MethodDescription addMethodDescription(MethodDescription m) {
        this.methods.add(m);
        return m;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        StringBuffer s= new StringBuffer(this.getClass().getName());
        s.append("(class= ").append(this.className).append(")@{\n");

        for (MethodDescription method: this.methods) {
            s.append("  - ").append(method.toString().replaceAll("\n", "\n  ")).append("\n");
        }
        return s.append("}").toString();
    }
}

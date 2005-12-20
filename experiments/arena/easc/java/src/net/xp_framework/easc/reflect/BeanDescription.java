/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import net.xp_framework.easc.reflect.InterfaceDescription;
import java.util.ArrayList;
import java.io.Serializable;

/**
 * Describes an EJB
 *
 */
public class BeanDescription implements Serializable {
    public String jndiName;
    public InterfaceDescription[] interfaces= new InterfaceDescription[2];
    
    public static final byte HOME   = 0;
    public static final byte REMOTE = 1;
    
    /**
     * Get JNDI name
     *
     * @access  public
     * @return  java.lang.String
     */
    public String getJndiName() {
        return this.jndiName;
    }
    
    /**
     * Set JNDI name
     *
     * @access  public
     * @param   java.lang.String name
     */
    public void setJndiName(String jndiName) {
        this.jndiName= jndiName;
    }
    
    /**
     * Set interface description
     *
     * @access  public
     * @param   byte type one of BeanDescription.HOME | BeanDescription.REMOTE
     * @param   net.xp_framework.easc.reflect.InterfaceDescription i
     * @return  net.xp_framework.easc.reflect.InterfaceDescription the description passed
     */
    public InterfaceDescription setInterfaceDescription(byte type, InterfaceDescription i) {
        this.interfaces[type]= i;
        return i;
    }

    /**
     * Get interface description
     *
     * @access  public
     * @param   byte type one of BeanDescription.HOME | BeanDescription.REMOTE
     * @return  net.xp_framework.easc.reflect.InterfaceDescription
     */
    public InterfaceDescription getInterfaceDescription(byte type) {
        return this.interfaces[type];
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return (new StringBuffer(this.getClass().getName()).
            append("(jndi= ").
            append(this.jndiName).
            append(")@{\n").
            append("  [Home  ]: ").
            append(null == this.interfaces[HOME] ? "(null)" : this.interfaces[HOME].toString().replaceAll("\n", "\n  ")).
            append('\n').
            append("  [Remote]: ").
            append(null == this.interfaces[REMOTE] ? "(null)" : this.interfaces[REMOTE].toString().replaceAll("\n", "\n  ")).
            append("\n}")
        ).toString();
    }
}

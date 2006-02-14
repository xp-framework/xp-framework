/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import net.xp_framework.easc.reflect.BeanDescription;
import java.util.HashMap;
import java.io.Serializable;

/**
 * Describes an EJB
 *
 */
public class DescriptionList implements Serializable {
    public HashMap<String, BeanDescription> beans= new HashMap<String, BeanDescription>();
    private transient HashMap<String, BeanDescription> copy= null;
    
    public synchronized void lock() {
        this.copy= (HashMap<String, BeanDescription>)this.beans.clone();
    }

    public synchronized void unlock() {
        this.beans.clear();
        this.beans= (HashMap<String, BeanDescription>)this.copy.clone();
        this.copy= null;
    }
    
    /**
     * Set description for a given bean.
     * 
     * @access  public
     * @param   java.lang.String name
     * @param   net.xp_framework.easc.reflect.BeanDescription description
     */
    public void add(String name, BeanDescription description) {
        this.copy.put(name, description);
    }

    /**
     * Get a description for a specified name
     * 
     * @access  public
     * @param   java.lang.String name
     * @return  net.xp_framework.easc.reflect.BeanDescription description
     */
    public BeanDescription of(String name) {
        return this.beans.get(name);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        StringBuffer s= new StringBuffer(this.getClass().getName());
        for (BeanDescription d: this.beans.values()) {
            s.append("- ").append(d.toString().replaceAll("\n", "\n  ")).append("\n");
        }
        return s.append('}').toString();
    }
}

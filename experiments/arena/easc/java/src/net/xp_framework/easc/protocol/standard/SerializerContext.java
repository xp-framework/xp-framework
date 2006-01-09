/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.util.HashSet;

/**
 * Indicates an error during serialization
 *
 * @see   net.xp_framework.easc.protocol.standard.Serializer
 */
public class SerializerContext extends Object {
    public HashSet<Class> classList= new HashSet<Class>();
    public ClassLoader classLoader= null;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.ClassLoader classLoader
     */
    public SerializerContext(ClassLoader classLoader) {
        this.classLoader= classLoader;
    }
    
    /**
     * Adds a class
     *
     * @access  public
     * @param   java.lang.Class c
     */
    public void addClass(Class c) {
        this.classList.add(c);
    }
}

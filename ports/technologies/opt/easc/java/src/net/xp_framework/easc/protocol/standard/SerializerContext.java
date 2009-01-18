/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

/**
 * Indicates an error during serialization
 *
 * @see   net.xp_framework.easc.protocol.standard.Serializer
 */
public class SerializerContext extends Object {
    public ClassLoader classLoader = null;
    public Object handler = null;
    
    /**
     * Constructor
     *
     * @param   java.lang.ClassLoader classLoader
     */
    public SerializerContext(ClassLoader classLoader) {
        this.classLoader= classLoader;
    }

    /**
     * Constructor
     *
     * @param   java.lang.ClassLoader classLoader
     * @param   java.lang.Object handler
     */
    public SerializerContext(ClassLoader classLoader, Object handler) {
        this.classLoader= classLoader;
        this.handler= handler;
    }

    /**
     * Constructor
     *
     * @param   java.lang.Object handler
     */
    public SerializerContext(Object handler) {
        this.classLoader= handler.getClass().getClassLoader();
        this.handler= handler;
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.util.HashMap;
import javax.naming.NameNotFoundException;

import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.reflect.DescriptionList;
import net.xp_framework.easc.reflect.BeanDescription;
import net.xp_framework.easc.reflect.ClassWrapper;

/**
 * Server context for the invocation server (EASC mbean)
 *
 */
public class ReflectionServerContext extends ServerContext {
    public DescriptionList descriptions;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   net.xp_framework.easc.reflect.DescriptionList descriptions
     */
    public ReflectionServerContext(DescriptionList descriptions) {
        this.descriptions= descriptions;
    }
    
    /**
     * Constructor
     *
     * @access  private
     * @param   net.xp_framework.easc.server.ServerContext ctx
     * @param   java.lang.String name
     * @param   net.xp_framework.easc.reflect.BeanDescription
     */
    private static BeanDescription descriptionOf(final ServerContext ctx, String name) {
        return ((ReflectionServerContext)ctx).descriptions.of(name);
    }

    /**
     * Retrieve lookup delegate
     * 
     * @access  public
     * @param   java.lang.String jndiName
     * @return  net.xp_framework.easc.server.Delegate
     */
    public Delegate lookup(final String jndiName) {
        return new Delegate() {
            public Object invoke(ServerContext ctx) throws Exception {
            
                // "Services": Look up services list
                if ("Services".equals(jndiName)) {
                    return ((ReflectionServerContext)ctx).descriptions;
                }
                
                // "Services:{service}": Lookup specific service
                if (jndiName.startsWith("Services:")) {
                    String serviceName= jndiName.substring("Services:".length(), jndiName.length());

                    return descriptionOf(ctx, serviceName);
                }
                
                
                // "Class:{service}:{classname}": Lookup class description
                if (jndiName.startsWith("Class:")) {
                    int offset= jndiName.indexOf(':', "Class:".length());
                    String serviceName= jndiName.substring("Class:".length(), offset);
                    String className= jndiName.substring(offset + 1, jndiName.length());
                    
                    return new ClassWrapper(descriptionOf(ctx, serviceName).getClassLoader().loadClass(className));
                }
                
                throw new NameNotFoundException("Name " + jndiName + " not bound");
            }
            
            public ClassLoader getClassLoader() {
                return null;
            }
        };
    }
}

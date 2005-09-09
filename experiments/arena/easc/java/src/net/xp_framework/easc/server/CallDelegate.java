/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.lang.reflect.Proxy;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ProxyMap;
import net.xp_framework.easc.protocol.standard.Serializer;
import java.util.HashMap;
import static net.xp_framework.easc.util.MethodMatcher.methodFor;

public class CallDelegate implements Delegate {
    private long objectId;
    private String methodName;
    private String serializedArguments;

    public CallDelegate(long objectId, String methodName, String serializedArguments) {
        this.objectId= objectId;
        this.methodName= methodName;
        this.serializedArguments= serializedArguments;
    }

    public Object invoke(ProxyMap map) throws Exception {
        Object instance= map.getObject(this.objectId);
        
        Object arguments[]= null;
        try {
            arguments= (Object[])Serializer.valueOf(this.serializedArguments, instance.getClass().getClassLoader());
        } catch (Exception e) {
            throw new Exception("Serialized data corrupt: " + e.getMessage());
        }
        
        Method method= methodFor(instance.getClass(), this.methodName, arguments);
        
        if (null == method) {
            throw new NoSuchMethodException("Method '" + this.methodName + "' not found");
        }
        
        Object result= method.invoke(instance, arguments);
        
        // If the result is a reference to a proxy, add it to our proxy list
        if (null != result && Proxy.isProxyClass(result.getClass())) {
            long identifier= map.put("(null)", result);
            return new ProxyWrapper(identifier, result);
        }

        return result;
    }
    
    /**
     * Creates a string representation of this delegate
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return (
            this.getClass().getName() + 
            "@(oid= " + this.objectId +
            ", method= " + this.methodName + 
            ", args= " + this.serializedArguments + 
            "))"
        );
    }
}

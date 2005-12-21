/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import net.xp_framework.easc.protocol.standard.ServerHandler;
import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.protocol.standard.Serializer;
import java.lang.reflect.Proxy;

public class InvocationServerHandler extends ServerHandler {
    
    /**
     * Setup this handler
     *
     * @access  public
     * @param   net.xp_framework.easc.server.ServerContext ctx
     */
    public void setup(final ServerContext ctx) {
        Serializer.registerExceptionName(javax.naming.NameNotFoundException.class, "naming/NameNotFound");
        Serializer.registerExceptionName(java.lang.reflect.InvocationTargetException.class, "invoke/Exception");

        Serializer.registerMapping(Proxy.class, new Invokeable<String, Proxy>() {
            public String invoke(Proxy p) throws Exception {
                ctx.objects.put(p.hashCode(), p);
                return "I:" + p.hashCode() + ":{" + Serializer.representationOf(
                    p.getClass().getInterfaces()[0].getName()
                ) + "}";
            }
        });
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import net.xp_framework.easc.protocol.standard.ServerHandler;
import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.protocol.standard.SerializerContext;
import javax.ejb.EJBHome;
import javax.ejb.EJBObject;
import java.lang.ref.WeakReference;

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

        Serializer.registerMapping(EJBHome.class, new Invokeable<String, EJBHome>() {
            public String invoke(EJBHome p, Object arg) throws Exception {
                ctx.objects.put(p.hashCode(), new WeakReference(p));
                
                // Find out the correct interface
                Class ejbInterface= null;
                for (Class iface: p.getClass().getInterfaces()) {
                    if (EJBHome.class.isAssignableFrom(iface)) {
                        ejbInterface= iface;
                        break;
                    }
                }

                return "I:" + p.hashCode() + ":{" + Serializer.representationOf(
                    ejbInterface.getName(),
                    (SerializerContext)arg
                ) + "}";
            }
        });
        Serializer.registerMapping(EJBObject.class, new Invokeable<String, EJBObject>() {
            public String invoke(EJBObject p, Object arg) throws Exception {
                ctx.objects.put(p.hashCode(), new WeakReference(p));
                
                // Find out the correct interface
                Class ejbInterface= null;
                for (Class iface: p.getClass().getInterfaces()) {
                    if (EJBObject.class.isAssignableFrom(iface)) {
                        ejbInterface= iface;
                        break;
                    }
                }

                return "I:" + p.hashCode() + ":{" + Serializer.representationOf(
                    ejbInterface.getName(),
                    (SerializerContext)arg
                ) + "}";
            }
        });
    }
}

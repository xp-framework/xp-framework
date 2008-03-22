/**
 * This file is part of the XP framework
 *
 * $Id$
 */
package net.xp_framework.easc.server.standalone;

import java.io.IOException;
import java.io.Serializable;
import java.lang.reflect.Proxy;
import java.net.InetAddress;
import java.net.ServerSocket;
import java.util.Hashtable;
import java.lang.ref.WeakReference;
import java.util.Properties;
import javax.ejb.EJBHome;

import javax.naming.Context;
import javax.naming.InitialContext;
import javax.naming.NamingException;
import javax.naming.spi.InitialContextFactory;
import javax.naming.spi.InitialContextFactoryBuilder;
import javax.naming.spi.NamingManager;

import net.xp_framework.easc.protocol.standard.InvocationServerHandler;
import net.xp_framework.easc.server.InvocationServerContext;
import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.server.ServerThread;
import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.protocol.standard.Invokeable;
import net.xp_framework.easc.protocol.standard.SerializerContext;

/**
 * EascServerThread provides means to start EASC threads and listen to given
 * interfaces and ports.
 *
 * JNDI is provided to bind objects into JNDI and have them remotely invokeable.
 * 
 */
public class EascServerThread {
    private ServerThread server = null;

    private InetAddress address = null;
    private int port = 14446;

    /**
     * Set port
     *
     */
    public void setPort(int port) {
        this.port= port;
    }
    
    /**
     * Retrieve port number
     *
     */
    public int getPort() {
        return this.port;
    }
    
    /**
     * Set address to listen on. Leave as null to bind to all
     * interfaces
     *
     */
    public void setAddress(InetAddress addr) {
        this.address= addr;
    }
    
    /**
     * Retrieve address to listen on
     *
     */
    public InetAddress getAddress() {
        return this.address;
    }
    
    /**
     * Perform static setup with global ServerContext
     *
     */
    public static void setup(final ServerContext ctx) {

        // Setup serializer
        Serializer.registerMapping(RemoteInvokeable.class,
            new Invokeable<String, RemoteInvokeable>() {
                public String invoke(RemoteInvokeable p, Object arg) throws Exception {
                    ctx.objects.put(p.hashCode(), new WeakReference<RemoteInvokeable>(p));
                        return "I:" + p.hashCode()
                            + ":{" + Serializer.representationOf(p.getClass().getName(), (SerializerContext) arg)
                            + "}"
                        ;
                }
        });
    }
    
    /**
     * Set up server context
     *
     */
    public void setUp() throws IOException {
        ServerContext sctx = new InvocationServerContext();
        EascServerThread.setup(sctx);
        
        // if address is null, ServerSocket will bind to all interfaces
        this.server= new ServerThread(new ServerSocket(this.port, 1, this.address));
        this.server.setHandler(new InvocationServerHandler());
        this.server.setContext(sctx);
    }
    
    /**
     * Start server thread
     *
     */
    public void start() {
        System.setProperty(Context.INITIAL_CONTEXT_FACTORY, "net.xp_framework.easc.server.standalone.StaticContextFactory");
        this.server.start(); 
    }
    
    /**
     * Shut down server
     *
     */
    public void shutdown() throws IOException {
        this.server.shutdown();
    }
    
    /**
     * Wait for server shutdown
     *
     */
    public void join() throws InterruptedException {
        this.server.join();
    }
    
    /**
     * Register bean in JNDI.
     *
     */
    public static void registerBean(String name, EJBHome instance) throws NamingException {
        java.util.List<Class> ejbIfaces= new java.util.ArrayList<Class>();
        for (Class iface: instance.getClass().getInterfaces()) {
            if (EJBHome.class.isAssignableFrom(iface)) {
                ejbIfaces.add(iface);
            }
        }
    
        new InitialContext().bind(
          name, 
          Proxy.newProxyInstance(
              instance.getClass().getClassLoader(), 
              ejbIfaces.toArray(new Class[]{}),
              new EASCInvocationHandler(instance)
        ));
    }
}

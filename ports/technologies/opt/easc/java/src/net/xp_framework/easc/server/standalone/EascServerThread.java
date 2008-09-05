/**
 * This file is part of the XP framework
 *
 * $Id$
 */
package net.xp_framework.easc.server.standalone;

import java.io.IOException;
import java.io.Serializable;
import java.io.InputStream;
import java.lang.reflect.Proxy;
import java.util.Hashtable;
import java.lang.ref.WeakReference;

import java.security.GeneralSecurityException;
import java.security.KeyStore;
import java.net.InetAddress;
import java.net.ServerSocket;
import javax.net.ServerSocketFactory;
import javax.net.ssl.SSLServerSocketFactory;
import javax.net.ssl.KeyManagerFactory;
import javax.net.ssl.SSLContext;

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
    protected ServerThread server = null;
    protected InetAddress address = null;
    protected int port            = 14446;
  
    // Members for SSL support
    protected boolean enableSsl   = false;
    protected KeyStore keyStore   = null;
    protected String keyStorePath = "resources/easc-default.keystore";
    protected String keyStorePass = "no-password";

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
     * Enable or disable SSL
     *
     */
    public void enableSsl(boolean enable) {
        this.enableSsl= enable;
    }    
    
    /**
     * Check whether SSL is en- or disabled.
     *
     */
    public boolean sslEnabled() {
        return this.enableSsl;
    }
    
    /**
     * Set new keystore to use for EASC. This overrides any system 
     * properties that may be defined.
     *
     */
    public void setKeyStore(KeyStore keyStore) {
        this.keyStore= keyStore;
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
     * Helper method to load keystore from resource
     *
     */
    protected KeyStore getKeyStore() throws IOException, GeneralSecurityException {
        if (null != this.keyStore)
            return this.keyStore;
        
        KeyStore keyStore= KeyStore.getInstance(KeyStore.getDefaultType());

        InputStream stream= null;
        try {

            // Load keyStore from locate where this class was loaded from
            stream= this.getClass().getClassLoader().getResourceAsStream(this.keyStorePath);
            keyStore.load(stream, this.keyStorePass.toCharArray());

            // Print out subject of used certificate
            java.util.Enumeration<String> e= keyStore.aliases();
            while (e.hasMoreElements()) {
                String alias= e.nextElement();
                System.out.println("---> Certificate: " + 
                    ((java.security.cert.X509Certificate)keyStore.getCertificate(alias)).
                        getSubjectX500Principal().getName()
                );
            }
        } finally {
            if (stream != null) {
                stream.close();
            }
        }
        
        return keyStore;
    }
    
    /**
     * Set up server context
     *
     */
    public void setUp() throws Exception {
        final String envKeyStore= "javax.net.ssl.keyStore";
        ServerContext sctx = new InvocationServerContext();
        EascServerThread.setup(sctx);
        ServerSocket ss= null;
        
        if (true == this.enableSsl) {
        
            // See http://javadoc.xp-framework.net//technotes/guides/security/jsse/JSSERefGuide.html
            SSLContext context= null;
        
            // If System property has been set, use given keystore, otherwise
            // use default keystore provided by EASC jar.
            if (null != this.keyStore || null == System.getProperty(envKeyStore)) {
            
                System.out.println("===> Setting up EASC default SSL certificate.");
                // Register keyStore in KeyManagerFactory used to initialize SSLContext
                KeyManagerFactory kmf= KeyManagerFactory.getInstance(KeyManagerFactory.getDefaultAlgorithm());
                kmf.init(this.getKeyStore(), this.keyStorePass.toCharArray());

                context= SSLContext.getInstance("SSLv3");
                context.init(kmf.getKeyManagers(), null, null);
            } else {
            
                // A keyStore has been set through system properties, so just use that...
                System.out.println("---> Using SSL keystore file: " + System.getProperty(envKeyStore));
                context= SSLContext.getInstance("Default");
            }

            ss= context.getServerSocketFactory().createServerSocket(this.port);
        } else {
            ss= new ServerSocket(this.port);
        }
        
        // if address is null, ServerSocket will bind to all interfaces
        this.server= new ServerThread(ss);
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

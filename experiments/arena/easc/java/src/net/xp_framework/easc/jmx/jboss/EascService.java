/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.jmx.jboss;

import org.jboss.system.ServiceMBeanSupport;
import net.xp_framework.easc.server.ServerThread;
import net.xp_framework.easc.protocol.standard.ServerHandler;
import net.xp_framework.easc.jmx.jboss.EascServiceMBean;
import java.net.ServerSocket;

/**
 * EASC service managed bean
 *
 * @see   org.jboss.system.ServiceMBeanSupport
 */
public class EascService extends ServiceMBeanSupport implements EascServiceMBean {
    private int port= 0;
    private ServerThread serverThread= null;
    
    /**
     * Sets the port the server thread will listen on
     *
     * @access  public
     * @param   int port
     */
    public void setPort(int port) {
        this.port= port;
    }

    /**
     * Gets the port the server thread is listening on
     *
     * @access  public
     * @return  int
     */
    public int getPort() {
        return this.port;
    }
    
    /**
     * Starts EASC service
     *
     * @access  protected
     * @throws  java.lang.Exception
     * @see     org.jboss.system.ServiceMBeanSupport#startService
     */
    protected void startService() throws Exception {
        this.serverThread= new ServerThread(new ServerSocket(this.port));
        this.serverThread.setHandler(new ServerHandler());
        this.serverThread.start();
    }
    
    /**
     * Starts EASC service
     *
     * @access  protected
     * @throws  java.lang.Exception
     * @see     org.jboss.system.ServiceMBeanSupport#stopService
     */
    protected void stopService() throws Exception {
        this.serverThread.shutdown();
    }
}

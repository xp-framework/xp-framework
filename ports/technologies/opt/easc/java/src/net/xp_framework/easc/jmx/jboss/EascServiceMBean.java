/* This class is part of the XP framework's EAS connectivity
 *
 * $Id: EascMBean.java 5613 2005-08-24 15:12:57Z friebe $
 */

package net.xp_framework.easc.jmx.jboss;

import org.jboss.system.ServiceMBean;

/**
 * EASC service managed bean interface
 *
 * @see   org.jboss.system.ServiceMBean
 */
public interface EascServiceMBean extends ServiceMBean {
    
    /**
     * Sets the port the server thread will listen on
     *
     * @access  public
     * @param   int port
     */
    public void setPort(int port);

    /**
     * Gets the port the server thread is listening on
     *
     * @access  public
     * @return  int
     */
    public int getPort();
}

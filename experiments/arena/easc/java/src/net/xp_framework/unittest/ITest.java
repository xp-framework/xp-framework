/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

/**
 * Test interface
 *
 * @purpose Base for Proxy generator
 */
public interface ITest extends javax.ejb.EJBHome {
    public Object hello();
    public Object hello(String name);
}

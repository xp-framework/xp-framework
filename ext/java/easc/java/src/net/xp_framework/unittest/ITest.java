/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import javax.ejb.EJBHome;

/**
 * Test interface
 *
 * @purpose Base for Proxy generator
 */
public interface ITest extends EJBHome {
    public Object hello();
    public Object hello(String name);
}

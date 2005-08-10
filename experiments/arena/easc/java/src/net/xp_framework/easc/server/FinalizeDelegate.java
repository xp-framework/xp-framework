/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;

public class FinalizeDelegate implements Delegate {

    public FinalizeDelegate() {
    }

    public Object invoke() throws Exception {
        return true;
    }
}

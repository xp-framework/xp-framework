/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.util.HashMap;
import javax.transaction.UserTransaction;

public class ServerContext {
    public HashMap<Integer, Object> objects= new HashMap<Integer, Object>();
    public UserTransaction transaction= null;
}

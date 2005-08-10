/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.io.IOException;
import java.io.DataInputStream;
import java.io.DataOutputStream;

public interface Handler {

    public void handle(DataInputStream in, DataOutputStream out) throws IOException;
}

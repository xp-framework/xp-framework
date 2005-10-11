/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.io.IOException;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import net.xp_framework.easc.server.ServerContext;

/**
 * Handler interface
 *
 */
public interface Handler {

    /**
     * Handle client
     *
     * @access  public
     * @param   java.io.DataInputStream in
     * @param   java.io.DataOutputStream out
     * @param   net.xp_framework.easc.server.ServerContext ctx
     * @throws  java.io.IOException
     */
    public void handle(DataInputStream in, DataOutputStream out, final ServerContext ctx) throws IOException;
}

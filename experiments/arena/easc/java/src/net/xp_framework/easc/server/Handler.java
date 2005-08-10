/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.io.IOException;
import java.io.DataInputStream;
import java.io.DataOutputStream;

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
     * @throws  java.io.IOException
     */
    public void handle(DataInputStream in, DataOutputStream out) throws IOException;
}

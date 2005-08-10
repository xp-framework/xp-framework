/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.io.IOException;
import java.net.Socket;
import java.io.DataOutputStream;
import java.io.DataInputStream;
import net.xp_framework.easc.server.Handler;

/**
 * The handler thread is started from within the server thread after a
 * call to ServerSocket.accept() to be able to handle multiple connections
 * simultaneously.
 *
 * @see   net.xp_framework.easc.server.ServerThread
 */
public class HandlerThread extends Thread {
    private Socket socket= null;
    private Handler handler= null;

    /**
     * Constructor
     *
     * @access  public
     * @param   net.xp_framework.easc.Handler handler
     * @param   java.net.Socket socket
     */
    public HandlerThread(Handler handler, Socket socket) {
        super("HandlerThread@{" + socket.getInetAddress().toString() + ":" + socket.getLocalPort() + "}");
        this.socket= socket;
        this.handler= handler;
    }

    /**
     * Thread's run method
     *
     * @access  public
     */
    @Override public void run() {
        try {
            this.handler.handle(
                new DataInputStream(this.socket.getInputStream()),
                new DataOutputStream(this.socket.getOutputStream())
            );
            this.socket.close();
        } catch (IOException ignored) { }
    }
}

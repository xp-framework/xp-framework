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
import net.xp_framework.easc.server.ServerContext;

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
    private ServerContext ctx= null;

    /**
     * Constructor
     *
     * @access  public
     * @param   net.xp_framework.easc.Handler handler
     * @param   java.net.Socket socket
     * @param   java.net.Socket socket
     * @param   net.xp_framework.easc.server.ServerContext ctx
     */
    public HandlerThread(Handler handler, Socket socket, ServerContext ctx) {
        super("HandlerThread@{" + socket.getInetAddress().toString() + ":" + socket.getLocalPort() + "}");
        this.socket= socket;
        this.handler= handler;
        this.ctx= ctx;
    }

    /**
     * Thread's run method
     *
     * @access  public
     */
    @Override public void run() {
        try {
            DataInputStream in= new DataInputStream(this.socket.getInputStream());
            DataOutputStream out= new DataOutputStream(this.socket.getOutputStream());
            
            // Run handler loop
            this.handler.handle(in, out, this.ctx);

            // Close streams
            in.close();
            out.close();
        } catch (IOException ignored) { 
        } finally {
            try {
                this.socket.close();
            } catch (IOException ignored) {
            }
        }
    }
}

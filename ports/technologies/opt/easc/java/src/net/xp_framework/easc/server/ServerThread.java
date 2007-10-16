/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.net.ServerSocket;
import java.net.Socket;
import java.io.IOException;
import java.util.HashMap;
import java.util.ArrayList;
import java.lang.ref.WeakReference;
import net.xp_framework.easc.server.Handler;
import net.xp_framework.easc.server.HandlerThread;
import net.xp_framework.easc.server.ServerContext;

/**
 * Server thread
 *
 * Usage:
 * <code>
 *   server= new ServerThread(new ServerSocket(6100, 50, InetAddress.getLocalHost()));
 *   server.setHandler(...);
 *   server.start();
 *
 *   // ...
 *
 *   server.shutdown();
 * </code>
 *
 * @see   net.xp_framework.easc.server.Handler
 * @see   java.net.ServerSocket
 */
public class ServerThread extends Thread {
    private boolean stopped= true;
    private ServerSocket socket= null;
    private Handler handler= null;
    private ServerContext context= null;
    private Thread referenceCleaner= null;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   java.net.ServerSocket socket
     */
    public ServerThread(ServerSocket socket) {
        super("ServerThread@{" + socket.getInetAddress().toString() + ":" + socket.getLocalPort() + "}");
        this.socket= socket;
    }
    
    static class ReferenceCleaner extends Thread {
        protected HashMap<Integer, WeakReference> list;
        
        public ReferenceCleaner(HashMap<Integer, WeakReference> list) {
            this.list= list;
        }

        @Override public void run() {
            ArrayList<Integer> candidates= new ArrayList<Integer>();
            do {
                try {
                    this.sleep(10000);
                } catch (InterruptedException e) {
                    // Shutdown this thread
                    return;
                }

                // Find possible candidates for removal
                candidates.clear();
                for (Integer hashCode: this.list.keySet()) {
                    if (this.list.get(hashCode).get() == null) {
                        candidates.add(hashCode);
                    }
                }
                
                // Remove candidates
                if (candidates.isEmpty()) continue;
                
                // DEBUG System.out.println("[EASC] Removing references " + candidates);
                for (Integer hashCode: candidates) {
                    this.list.remove(hashCode);
                }
            } while (true);
        }   
    }

    /**
     * Set handler for accepted connections
     *
     * @access  public
     * @param   net.xp_framework.easc.server.Handler handler
     */
    public void setHandler(Handler handler) {
        this.handler= handler;
    }

    /**
     * Set handler for accepted connections
     *
     * @access  public
     * @param   net.xp_framework.easc.server.ServerContext context
     */
    public void setContext(ServerContext context) {
        this.context= context;
    }

    /**
     * Thread's run method
     *
     * @access  public
     */
    @Override public void run() {
        this.stopped= false;   // We're running:)
        
        // Setup handler
        this.handler.setup(this.context);
        
        this.referenceCleaner= new ReferenceCleaner(this.context.objects);
        this.referenceCleaner.start();
        
        // Loop until stopped, accepting incoming connections
        while (!this.stopped) {
            Socket accepted= null;
             
            try {
                accepted= this.socket.accept();
                accepted.setTcpNoDelay(true);

                // Create a new thread that will handle this client
                (new HandlerThread(this.handler, accepted, this.context)).start();
            } catch (IOException ignored) { }
        }
    }
    
    /**
     * Shut down the server
     *
     * @access  
     * @param   
     * @return  
     */
    public synchronized void shutdown() throws IOException {
        this.referenceCleaner.stop();
        this.socket.close();
        this.stopped= true;
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import java.net.InetAddress;
import java.net.ServerSocket;
import java.net.Socket;
import java.net.SocketException;
import java.net.ConnectException;
import java.net.InetSocketAddress;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.EOFException;
import java.util.ArrayList;
import org.junit.Test;
import org.junit.BeforeClass;
import org.junit.AfterClass;
import org.junit.After;
import net.xp_framework.easc.util.ByteCountedString;

import static org.junit.Assert.*;


/**
 * Test server
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.MessageType
 */
public class ThreadingTest {
    protected static final int SERVER_PORT = 9999;
    protected static final int SERVER_COUNT = 3;     // Maximum three clients in parallel
    protected static final int SERVER_BACKLOG = 3;   // Three more waiting, if we exceed that, connection refused

    protected static InetAddress serverAddress = null;
    protected static ServerSocket serverSocket = null;
    
    static class ListenerThread extends Thread {
        protected ServerSocket socket = null;
        protected boolean stopped = false;
        protected String status = "STARTED";
        
        /**
         * Constructor
         */    
        public ListenerThread(ServerSocket socket, int number) throws IOException {
            super("Listener #" + number);
            this.socket= socket;
        }
        
        /**
         * Close a socket (NPE - safe)
         */    
        protected void closeSocket(Socket s) {
            if (s == null) return;
            try {
                s.close();
            } catch (IOException io) {
                io.printStackTrace();
            }
            s = null;
        }
        
        /**
         * Status accessor
         */    
        protected void setStatus(String s) {
            this.status= s;
            System.out.println(this.getName() + " - " + s);
        }
        
        /**
         * Status accessor
         */    
        public String getStatus() {
            return this.status;
        }
        
        /**
         * Run server thread
         */    
        @Override 
        public void run() {
            while (!this.stopped) {
                Socket accepted= null;

                try {
                    this.setStatus("ACCEPTING...");

                    accepted= this.socket.accept();
                    accepted.setTcpNoDelay(true);
                    
                    this.setStatus("ACCEPTED " + accepted);
                    
                    // {{{ communication
                    String message= ByteCountedString.readFrom(new DataInputStream(accepted.getInputStream()));
                    this.setStatus("MESSAGE '" + message + "'");
                    new ByteCountedString(message).writeTo(new DataOutputStream(accepted.getOutputStream()));
                    // }}}

                } catch (SocketException se) {

                    // Server socket closed, respond to shutdown
                    this.setStatus("SHUTDOWN " + accepted);
                    this.stopped= true;
                } catch (EOFException ee) {

                    // Client has disconnected
                    this.setStatus("HANGUP " + accepted);
                } catch (IOException oe) {
                
                    // Something 
                    this.setStatus("EXCEPTION " + oe);
                } finally {

                    this.setStatus("CLOSE " + accepted);
                    this.closeSocket(accepted);
                }
            }
        }

        /**
         * Shutdown server thread.
         */    
        public void shutdown() throws InterruptedException {
            this.setStatus("STOPPING");
            this.stopped= true;
            this.join();
            this.setStatus("STOPPED");
        }
    }

    protected static ArrayList<ListenerThread> servers= new ArrayList<ListenerThread>();
    
    /**
     * Wait for a given status - all listeners must be in this status before this
     * method returns!
     *
     * Will timeout eventually...
     */    
    protected static void waitFor(String status) {
        int times= 0;
        for (int i= 0; i < SERVER_COUNT; i++) {
            if (servers.get(i).getStatus().equals(status)) continue;
            
            times++;
            try {
                Thread.sleep(100);
            } catch (InterruptedException e) { 
                if (times > SERVER_COUNT * 10) {
                    fail(e.getMessage());
                }
            }
        }
        System.out.println("All listeners now in " + status);
    }
    
    /**
     * Create server thread. Executed before test class is instanciated
     */    
    @BeforeClass public static void createServerThread() throws Exception {
        serverAddress= InetAddress.getLocalHost();
        serverSocket= new ServerSocket(SERVER_PORT, SERVER_BACKLOG, serverAddress);

        for (int i= 0; i < SERVER_COUNT; i++) {
            ListenerThread l= new ListenerThread(serverSocket, i);
            l.start();
            servers.add(l);
        }
        waitFor("ACCEPTING...");
    }
    
    @After public void allListenersMustBeAccepting() {
        waitFor("ACCEPTING...");
    }

    /**
     * Stop server thread. Executed after all tests have run
     */    
    @AfterClass public static void stopServerThread() throws Exception {
        System.out.println("Shutdown!");
        serverSocket.close();
        for (int i= 0; i < SERVER_COUNT; i++) {
            servers.get(i).shutdown();
        }
        waitFor("STOPPED");
    }
    
    /**
     * Helper method which will communicate with the server
     */
    public void assertCommunications(Socket s, String message) {
        String reply= null;
        
        try {
            new ByteCountedString(message).writeTo(new DataOutputStream(s.getOutputStream()));
            reply= ByteCountedString.readFrom(new DataInputStream(s.getInputStream()));
        } catch (IOException e) {
            fail(e.getMessage());
        }
        
        assertEquals(message, reply);
    }

    /**
     * Helper method which will open a specified amount of sockets and 
     * communicate with the server
     */
    public void assertConnections(int number) throws IOException {
        ArrayList<Socket> clients= new ArrayList<Socket>();
        
        // Connect # of sockets using 2 seconds timeout
        for (int i= 0; i < number; i++) {
            try {
                Socket s= new Socket();
                s.connect(new InetSocketAddress(serverAddress, SERVER_PORT), 2000);
                s.setTcpNoDelay(true);
                clients.add(s);
            } catch (IOException e) {
                System.out.println("Socket #" + i + " ~ " + e);
                while (i-- > 0) {
                    clients.get(i).close();
                }
                clients.clear();   
                throw new ConnectException("#" + i + ": " + e.getMessage());
            }
        }

        try {
            for (int i= 0; i < number; i++) {
                this.assertCommunications(clients.get(i), "#" + i);
            }
        } finally {
            for (int i= 0; i < number; i++) {
                clients.get(i).close();
            }
            clients.clear();
        }
    }

    /**
     * Tests one client
     */
    @Test public void oneClient() throws Exception {
        System.out.println("oneClient():");
        this.assertConnections(1);
    }

    /**
     * Tests broken client (connects, disconnects immediately)
     */
    @Test public void brokenClient() throws Exception {
        System.out.println("brokenClient():");
        Socket s= new Socket(serverAddress, SERVER_PORT);
        s.close();
    }

    /**
     * Tests broken client #2 (connects, writes, but then disconnects immediately)
     */
    @Test public void brokenClientTwo() throws Exception {
        System.out.println("brokenClientTwo():");
        Socket s= new Socket(serverAddress, SERVER_PORT);
        new ByteCountedString("BORK").writeTo(new DataOutputStream(s.getOutputStream()));
        s.close();
    }

    /**
     * Tests two clients
     */
    @Test public void twoClients() throws Exception {
        System.out.println("twoClients():");
        this.assertConnections(2);
    }
    
    /**
     * Tests SERVER_COUNT clients
     */
    @Test public void serverCountClients() throws Exception {
        System.out.println("serverCountClients():");
        this.assertConnections(SERVER_COUNT);
    }

    /**
     * Tests more than SERVER_COUNT clients (SERVER_BACKLOG)
     */
    @Test public void moreThanServerCountClients() throws Exception {
        int total= SERVER_COUNT + SERVER_BACKLOG;
        System.out.println("moreThanServerCountClients(" + total + "):");
        this.assertConnections(total);
    }

    /**
     * Tests more than SERVER_COUNT clients (SERVER_BACKLOG + 1)
     */
    @Test(expected= java.net.ConnectException.class) public void connectionsExceeded() throws Exception {
        System.out.println("connectionsExceeded():");
        
        this.assertConnections(SERVER_COUNT + SERVER_BACKLOG + 1);
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.unittest;

import org.junit.Test;
import org.junit.BeforeClass;
import org.junit.AfterClass;
import java.net.InetAddress;
import java.net.ServerSocket;
import java.net.Socket;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.util.Hashtable;
import java.util.Date;
import net.xp_framework.easc.server.ServerThread;
import net.xp_framework.easc.server.Handler;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.protocol.standard.MessageType;
import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.unittest.MockContextFactory;
import net.xp_framework.easc.unittest.Person;
import net.xp_framework.easc.server.Delegate;
import javax.naming.InitialContext;
import javax.naming.spi.NamingManager;
import javax.naming.spi.InitialContextFactoryBuilder;
import javax.naming.spi.InitialContextFactory;

import static org.junit.Assert.*;

/**
 * Test server
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.MessageType
 */
public class ServerTest {
    private final static int TEST_PORT= 14446;
    private static ServerThread server= null;
    private static Socket client= null;

    abstract class Writer {
        abstract public void writeTo(DataOutputStream out) throws IOException;
    }
    
    /**
     * Create server thread. Executed before test class is instanciated
     *
     * @static
     * @access  public
     */    
    @BeforeClass public static void createServerThread() throws Exception {
        InetAddress addr= InetAddress.getLocalHost();

        // Create server, set handler and start it!
        server= new ServerThread(new ServerSocket(TEST_PORT, 1, addr));
        server.setHandler(new Handler() {
            public void handle(DataInputStream in, DataOutputStream out) throws IOException {
                while (true) {
                    Header h= Header.readFrom(in);

                    // Verify magic number
                    if (Header.DEFAULT_MAGIC_NUMBER != h.getMagicNumber()) {
                        out.writeUTF("-ERR MAGIC");
                        out.flush();
                        break;
                    }
                    
                    System.out.print(h.getMessageType() + " => ");
                    Delegate delegate= h.getMessageType().delegateFrom(in);
                    
                    try {
                        out.writeUTF("+OK " + delegate.getClass().getName() + ": " + Serializer.representationOf(delegate.invoke()));
                    } catch (Exception e) {
                        e.printStackTrace(System.err);
                        out.writeUTF("-ERR " + e.getClass().getName());
                    }
                    
                    out.flush();
                }
            }
        });
        server.start();
        
        // Set up client socket
        client= new Socket(addr, TEST_PORT);

        // Set Mock context as initial
        NamingManager.setInitialContextFactoryBuilder(new InitialContextFactoryBuilder() {
            public InitialContextFactory createInitialContextFactory(Hashtable<?,?> environment) {
                return new MockContextFactory();
            }
        });
        
        // Bind test objects
        InitialContext ctx= new InitialContext();
        ctx.bind("test/DateObject", new Date(1123681953000L));
        ctx.bind("test/PersonObject", new Person());
    }

    /**
     * Stop server thread. Executed after all tests have run
     *
     * @static
     * @access  public
     */    
    @AfterClass public static void stopServerThread() throws Exception {
        client.close();
        server.shutdown();
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   java.lang.String expected
     * @param   net.xp_framework.easc.protocol.standard.Header expected
     * @param   Writer writer
     */
    protected void assertAnswer(String expected, Header header, Writer writer) throws Exception {
        DataOutputStream out= new DataOutputStream(client.getOutputStream());
        
        header.writeTo(out);
        if (null != writer) writer.writeTo(out);
        out.flush();
        assertEquals(expected, (new DataInputStream(client.getInputStream())).readUTF());
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   java.lang.String expected
     * @param   net.xp_framework.easc.protocol.standard.Header expected
     */
    protected void assertAnswer(String expected, Header header) throws Exception {
        assertAnswer(expected, header, null);
    }

    /**
     * Tests the Initialize message
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void initializeMessage() throws Exception {
        assertAnswer("+OK net.xp_framework.easc.server.InitializationDelegate: b:1;", new Header(
            Header.DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            MessageType.Initialize,
            true,
            0
        ));
    }

    /**
     * Tests the Status message
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void statusMessage() throws Exception {
        assertAnswer("+OK net.xp_framework.easc.server.StatusDelegate: N;", new Header(
            Header.DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            MessageType.Status,
            true,
            0
        ));
    }

    /**
     * Tests lookup of a java.util.Date object
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void lookupDate() throws Exception {
        assertAnswer(
            "+OK net.xp_framework.easc.server.LookupDelegate: T:1123681953;", 
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Lookup,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeUTF("test/DateObject");
                }
            }
        );
    }

    /**
     * Tests lookup of a value object
     *
     * @see     net.xp_framework.easc.unittest.Person
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void lookupPerson() throws Exception {
        assertAnswer(
            "+OK net.xp_framework.easc.server.LookupDelegate: O:37:\"net.xp_framework.easc.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}", 
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Lookup,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeUTF("test/PersonObject");
                }
            }
        );
    }

    /**
     * Test that sending a wrong magic number will return an error
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void wrongMagicNumber() throws Exception {
        assertAnswer("-ERR MAGIC", new Header(
            -1,
            (byte)1,
            (byte)0,
            MessageType.Status,
            true,
            0
        ));
    }
}

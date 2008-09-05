/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.BeforeClass;
import org.junit.AfterClass;
import java.net.InetAddress;
import java.net.ServerSocket;
import java.net.Socket;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.Serializable;
import java.util.Hashtable;
import java.util.Date;
import java.lang.reflect.Proxy;
import net.xp_framework.easc.server.ServerThread;
import net.xp_framework.easc.server.InvocationServerContext;
import net.xp_framework.easc.protocol.standard.InvocationServerHandler;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.protocol.standard.MessageType;
import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.util.ByteCountedString;
import net.xp_framework.unittest.MockContextFactory;
import net.xp_framework.unittest.Person;
import net.xp_framework.unittest.ITest;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.unittest.DebugInvocationHandler;
import javax.naming.InitialContext;
import javax.naming.spi.NamingManager;
import javax.naming.spi.InitialContextFactoryBuilder;
import javax.naming.spi.InitialContextFactory;

import static org.junit.Assert.*;
import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

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
        server.setHandler(new InvocationServerHandler());
        server.setContext(new InvocationServerContext());
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
        ctx.bind("test/Interface", Proxy.newProxyInstance(
            ITest.class.getClassLoader(),
            new Class[] { ITest.class, Serializable.class },
            new DebugInvocationHandler()
        ));
        
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
     * @param   net.xp_framework.easc.protocol.standard.MessageType expectedType
     * @param   java.lang.String expectedData
     * @param   net.xp_framework.easc.protocol.standard.Header expected
     * @param   Writer writer
     */
    protected void assertAnswer(MessageType expectedType, String expectedData, Header header, Writer writer) throws Exception {
        
        // Write request
        DataOutputStream out= new DataOutputStream(client.getOutputStream());
        header.writeTo(out);
        if (null != writer) writer.writeTo(out);
        out.flush();
        
        // Read response
        DataInputStream in= new DataInputStream(client.getInputStream());
        assertEquals(expectedType, Header.readFrom(in).getMessageType());
        String actualData= ByteCountedString.readFrom(in);
        if (null != expectedData) assertEquals(expectedData, actualData);
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   net.xp_framework.easc.protocol.standard.MessageType expectedType
     * @param   java.lang.String expectedData
     * @param   net.xp_framework.easc.protocol.standard.Header header
     */
    protected void assertAnswer(MessageType expectedType, String expectedData, Header header) throws Exception {
        assertAnswer(expectedType, expectedData, header, null);
    }

    /**
     * Tests the Initialize message
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void initializeMessage() throws Exception {
        assertAnswer(
            MessageType.Value,
            "b:1;", 
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Initialize,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeBoolean(false);    // No authorization
                }
            }
        );
    }

    /**
     * Tests lookup of a java.util.Date object
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void lookupDate() throws Exception {
        assertAnswer(
            MessageType.Value,
            "T:1123681953;", 
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
                    new ByteCountedString("test/DateObject").writeTo(out);
                }
            }
        );
    }

    /**
     * Tests lookup of a value object
     *
     * @see     net.xp_framework.unittest.Person
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void lookupPerson() throws Exception {
        assertAnswer(
            MessageType.Value,
            "O:32:\"net.xp_framework.unittest.Person\":3:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";s:16:\"responsibilities\";A:2:{s:6:\"Leader\";s:10:\"Programmer\";}}", 
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
                    new ByteCountedString("test/PersonObject").writeTo(out);
                }
            }
        );
    }

    /**
     * Tests lookup of a Proxy instance
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void lookupProxy() throws Exception {
        assertAnswer(
            MessageType.Value,
            "I:1:{s:31:\"net.xp_framework.unittest.ITest\";}", 
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
                    new ByteCountedString("test/Interface").writeTo(out);
                }
            }
        );
    }

    /**
     * Tests method call on object id #1 (has been "created" by lookupProxy() before).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void helloMethodCall() throws Exception {
        assertAnswer(
            MessageType.Value,
            "s:122:\"Invoked method public abstract java.lang.Object net.xp_framework.unittest.ITest.hello(java.lang.String) with 1 argument(s)\";",
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Call,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeLong(1);
                    new ByteCountedString("hello").writeTo(out);
                    new ByteCountedString("A:1:{s:5:\"World\";}").writeTo(out);
                }
            }
        );
    }

    /**
     * Tests method call on object id #1 (has been "created" by lookupProxy() before).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void nonExistantMethodCall() throws Exception {
        assertAnswer(
            MessageType.Exception,
            null,
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Call,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeLong(1);
                    new ByteCountedString("nonExistant").writeTo(out);
                    new ByteCountedString("A:0:{}").writeTo(out);
                }
            }
        );
    }

    /**
     * Tests method call on object id #1 (has been "created" by lookupProxy() before).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void methodCallWithArgumentMismatch() throws Exception {
        assertAnswer(
            MessageType.Exception,
            null,
            new Header(
                Header.DEFAULT_MAGIC_NUMBER,
                (byte)1,
                (byte)0,
                MessageType.Call,
                true,
                0
            ),
            new Writer() {
                public void writeTo(DataOutputStream out) throws IOException {
                    out.writeLong(1);
                    new ByteCountedString("hello").writeTo(out);
                    new ByteCountedString("A:1:{i:1;}").writeTo(out);
                }
            }
        );
    }

    /**
     * Test that sending a wrong magic number will return an error. 
     *
     * Note: Must be the last test in this class for the server will 
     * close the connection!
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void wrongMagicNumber() throws Exception {
        assertAnswer(
            MessageType.Error,
            "Magic number mismatch", 
            new Header(
                -1,
                (byte)1,
                (byte)0,
                MessageType.Lookup,
                true,
                0
            )
        );
    }
}

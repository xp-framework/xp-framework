/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.protocol.standard.MessageType;
import java.io.ByteArrayOutputStream;
import java.io.ByteArrayInputStream;
import java.io.DataOutputStream;
import java.io.DataInputStream;

import static org.junit.Assert.*;

/**
 * Test types enumeration
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.Header
 */
public class HeaderTest {
    private Header header= new Header(
        Header.DEFAULT_MAGIC_NUMBER,
        (byte)1,
        (byte)0,
        MessageType.Initialize,
        true,
        0
    );

    /**
     * Tests writing a header results in 12 bytes on the wire
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void write() throws Exception {
        assertEquals(12, this.header.writeTo(new DataOutputStream(new ByteArrayOutputStream())));
    }
    
    /**
     * Tests reading a previously written header results in the 
     * "same" header when read (that is, that Header.equals() returns
     * true).
     *
     * @see     net.xp_framework.easc.protocol.standard.Header#equals
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void read() throws Exception {
        ByteArrayOutputStream out= new ByteArrayOutputStream();
        this.header.writeTo(new DataOutputStream(out));

        ByteArrayInputStream in= new ByteArrayInputStream(out.toByteArray());
        assertEquals(this.header, Header.readFrom(new DataInputStream(in)));
    }
    
    /**
     * Tests that Header constructor will throw a NullPointerException
     * when passed null for its messageType argument
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test(expected= NullPointerException.class) public void nullType() throws Exception {
        new Header(
            Header.DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            null,
            true,
            0
        );
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Before;
import net.xp_framework.easc.util.ByteCountedString;
import junit.framework.ComparisonFailure;
import java.io.ByteArrayOutputStream;
import java.io.ByteArrayInputStream;
import java.io.DataOutputStream;
import java.io.DataInputStream;

import static org.junit.Assert.*;

/**
 * Test byte counted string class
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.util.ByteCountedString
 */
public class ByteCountedStringTest {
    protected ByteCountedString string;

    /**
     * Setup method
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Before public void setUp() throws Exception {
        this.string= new ByteCountedString("This is a test text, used for this unittest");
    }
    
    /**
     * Escapes all ASCII characters with an ordinal value of less than 32
     * or more than 127 with a backslash.
     *
     * @static
     * @access  protected
     * @param   java.lang.String in
     * @return  java.lang.String
     */
    protected static String escapeSpecialCharacters(String in) {
        char c;
        int length= in.length();
        StringBuffer s= new StringBuffer(length);

        for (int i= 0; i < length; i++) {
            c= in.charAt(i);
            
            if (c < 0x20 || c > 0x7f) {
                s.append('\\').append((int)c);
            } else {
                s.append(c);
            }
        }
        return s.toString();
    }
    
    /**
     * Assert to strings are equal. Just like assertEquals(String, String) 
     * but escapes the strings using escapeSpecialCharacters() in the
     * ComparisonFailure thrown. 
     *
     * @static
     * @access  protected
     * @param   java.lang.String expected
     * @param   java.lang.String actual
     * @throws  junit.framework.ComparisonFailure in case the 
     */
    protected static void assertString(String expected, String actual) throws ComparisonFailure {
        if (
            (null == expected && null == actual) ||
            (null != expected && expected.equals(actual))
        ) return;

        throw new ComparisonFailure(
            null, 
            escapeSpecialCharacters(expected), 
            escapeSpecialCharacters(actual)
        );
    }
    
    /**
     * Tests the writeTo() method without arguments
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeDefaultChunks() throws Exception {
        ByteArrayOutputStream out= new ByteArrayOutputStream();

        this.string.writeTo(new DataOutputStream(out));
        assertEquals(46, this.string.length());
        assertString(
            "\u0000\u002b\u0000This is a test text, used for this unittest",
            out.toString()
        );
    }

    /**
     * Tests the writeTo() method without a chunk size of 20
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeSmallChunks() throws Exception {
        ByteArrayOutputStream out= new ByteArrayOutputStream();

        this.string.writeTo(new DataOutputStream(out), 20);
        assertEquals(52, this.string.length(20));
        assertString(
            "\u0000\u0014\u0001This is a test text,\u0000\u0014\u0001 used for this unitt\u0000\u0003\u0000est",
            out.toString()
        );
    }

    /**
     * Tests the writeTo() method without a chunk size of 43
     * (the exact length of the test string)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeStringLengthChunks() throws Exception {
        ByteArrayOutputStream out= new ByteArrayOutputStream();

        this.string.writeTo(new DataOutputStream(out), 43);
        assertEquals(46, this.string.length(43));
        assertString(
            "\u0000\u002b\u0000This is a test text, used for this unittest",
            out.toString()
        );
    }

    /**
     * Tests the writeTo() method without arguments on a string 
     * with exactly 65535 bytes (which is the maximum length that
     * can be encoded in two bytes.
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeMaximumLengthChunks() throws Exception {
        StringBuffer s= new StringBuffer(65535);
        for (int i= 0; i < 65535; i++) {
            s.append('c');
        }
        
        ByteCountedString bcs= new ByteCountedString(s);
        ByteArrayOutputStream out= new ByteArrayOutputStream();
        bcs.writeTo(new DataOutputStream(out));
        String bytes= out.toString();

        assertEquals(65538, bcs.length());
        assertEquals(65538, bytes.length());
        assertString("\u00FF\u00FF\u0000ccccccc", bytes.substring(0, 10));
    }

    /**
     * Tests the writeTo() method without arguments on a string 
     * with more than 65535 bytes (exactly one more).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeMaximumLengthExceededChunks() throws Exception {
        StringBuffer s= new StringBuffer(65536);
        for (int i= 0; i < 65536; i++) {
            s.append('c');
        }
        
        ByteCountedString bcs= new ByteCountedString(s);
        ByteArrayOutputStream out= new ByteArrayOutputStream();
        bcs.writeTo(new DataOutputStream(out));
        String bytes= out.toString();

        assertEquals(65542, bcs.length());
        assertEquals(65542, bytes.length());    // 65536 + 2 * 3 control bytes
        assertString("\u00FF\u00FF\u0001ccccccc", bytes.substring(0, 10));
        assertString("\u0000\u0001\u0000c", bytes.substring(65538, 65542));
    }

    /**
     * Tests the writeTo() method returns UTF-8
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void writeReturnsUtf8() throws Exception {
        ByteCountedString bcs= new ByteCountedString("\u00FC");
        ByteArrayOutputStream out= new ByteArrayOutputStream();
        bcs.writeTo(new DataOutputStream(out));
        String bytes= out.toString();
        
        assertString("\u0000\u0002\u0000\u00C3\u00BC", bytes);
    }
    
    /**
     * Tests the readFrom() method without arguments on a string 
     * with more than 65535 bytes (exactly one more).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void readDefaultChunks() throws Exception {
        ByteArrayInputStream in= new ByteArrayInputStream(
            (byte[])"\u0000\u002b\u0000This is a test text, used for this unittest".getBytes()
        );
        String read= ByteCountedString.readFrom(new DataInputStream(in));
        
        assertEquals(43, read.length());
        assertEquals("This is a test text, used for this unittest", read);
    }    

}

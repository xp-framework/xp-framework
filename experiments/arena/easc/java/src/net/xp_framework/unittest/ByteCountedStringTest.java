/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Before;
import net.xp_framework.easc.util.ByteCountedString;
import junit.framework.ComparisonFailure;

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
     * with a backslash.
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
            
            if (c < 0x20) {
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
     * Tests the getChunks() method without arguments
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void defaultChunks() throws Exception {
        assertString(
            "\u0000\u002b\u0000This is a test text, used for this unittest",
            this.string.getBytes()
        );
    }

    /**
     * Tests the getChunks() method without a chunk size of 20
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void smallChunks() throws Exception {
        assertString(
            "\u0000\u0014\u0001This is a test text,\u0000\u0014\u0001 used for this unitt\u0000\u0003\u0000est",
            this.string.getBytes(20)
        );
    }

    /**
     * Tests the getChunks() method without a chunk size of 43
     * (the exact length of the test string)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void stringLengthChunks() throws Exception {
        assertString(
            "\u0000\u002b\u0000This is a test text, used for this unittest",
            this.string.getBytes(43)
        );
    }

    /**
     * Tests the getChunks() method without arguments on a string 
     * with exactly 65535 bytes (which is the maximum length that
     * can be encoded in two bytes.
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void maximumLengthChunks() throws Exception {
        StringBuffer s= new StringBuffer(65535);
        for (int i= 0; i < 65535; i++) {
            s.append('c');
        }
        
        String bytes= new ByteCountedString(s).getBytes();

        assertEquals(65538, bytes.length());
        assertString("\u00FF\u00FF\u0000ccccccc", bytes.substring(0, 10));
    }

    /**
     * Tests the getChunks() method without arguments on a string 
     * with more than 65535 bytes (exactly one more).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void maximumLengthExceededChunks() throws Exception {
        StringBuffer s= new StringBuffer(65536);
        for (int i= 0; i < 65536; i++) {
            s.append('c');
        }
        
        String bytes= new ByteCountedString(s).getBytes();

        assertEquals(65542, bytes.length());    // 65536 + 2 * 3 control bytes
        assertString("\u00FF\u00FF\u0001ccccccc", bytes.substring(0, 10));
        assertString("\u0000\u0001\u0000c", bytes.substring(65538, 65542));
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import net.xp_framework.easc.protocol.standard.MessageType;

import static org.junit.Assert.*;

/**
 * Test types enumeration
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.MessageType
 */
public class TypeTest {

    /**
     * Tests the valueOf() method for request messages
     *
     * @see     net.xp_framework.easc.protocol.standard.MessageType
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void requestMessages() throws Exception {
        assertEquals(MessageType.Initialize, MessageType.valueOf(0));
        assertEquals(MessageType.Lookup, MessageType.valueOf(1));
        assertEquals(MessageType.Call, MessageType.valueOf(2));
        assertEquals(MessageType.Finalize, MessageType.valueOf(3));
    }
    
    /**
     * Tests the valueOf() method for response messages
     *
     * @see     net.xp_framework.easc.protocol.standard.MessageType
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void responseMessages() throws Exception {
        assertEquals(MessageType.Value, MessageType.valueOf(4));
        assertEquals(MessageType.Exception, MessageType.valueOf(5));
        assertEquals(MessageType.Error, MessageType.valueOf(6));
    }

    /**
     * Tests the valueOf() method when passed an invalid (out-of-range) 
     * messagetype.
     *
     * @see     net.xp_framework.easc.protocol.standard.MessageType
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void invalidMessageIdentifier() throws Exception {
        assertEquals(null, MessageType.valueOf(6100));
    }
}

/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.unittest;

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
     * Tests the valueOf() method
     *
     * @see     net.xp_framework.easc.protocol.standard.MessageType#valueOf
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOf() throws Exception {
        assertEquals(MessageType.Initialize, MessageType.valueOf(0));
        assertEquals(MessageType.Status, MessageType.valueOf(1));
        assertEquals(MessageType.Lookup, MessageType.valueOf(2));
        assertEquals(MessageType.Call, MessageType.valueOf(3));
        assertEquals(MessageType.Value, MessageType.valueOf(4));
        assertEquals(MessageType.Finalize, MessageType.valueOf(5));
        assertEquals(null, MessageType.valueOf(6100));
    }
}

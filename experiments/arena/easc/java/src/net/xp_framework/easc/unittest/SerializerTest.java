/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.unittest;

import org.junit.Test;
import static org.junit.Assert.*;
import static net.xp_framework.easc.protocol.standard.Serializer.*;

public class SerializerTest {

    @Test public void serializeString() {
        assertEquals("s:11:\"Hello World\";", serialize("Hello World"));
    }

    @Test public void serializeIntPrimitive() {
        assertEquals("i:6100;", serialize(6100));
        assertEquals("i:-6100;", serialize(-6100));
    }

    @Test public void serializeLongPrimitive() {
        assertEquals("i:6100;", serialize(6100L));
        assertEquals("i:-6100;", serialize(-6100L));
    }
    
    @Test public void serializeBoolean() {
        assertEquals("b:1;", serialize(true));
        assertEquals("b:0;", serialize(false));
    }
}

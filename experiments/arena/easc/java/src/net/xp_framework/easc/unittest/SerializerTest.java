/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.unittest;

import org.junit.Test;
import org.junit.Ignore;
import net.xp_framework.easc.unittest.Person;
import net.xp_framework.easc.protocol.standard.Invokeable;
import java.util.HashMap;
import java.util.Date;
import java.util.UUID;
import java.util.Arrays;

import static org.junit.Assert.*;
import static net.xp_framework.easc.protocol.standard.Serializer.representationOf;
import static net.xp_framework.easc.protocol.standard.Serializer.valueOf;
import static net.xp_framework.easc.protocol.standard.Serializer.registerMapping;

public class SerializerTest {

    @Test public void representationOfString() throws Exception {
        assertEquals("s:11:\"Hello World\";", representationOf("Hello World"));
    }

    @Test public void representationOfCharPrimitive() throws Exception {
        assertEquals("s:1:\"X\";", representationOf('X'));
    }

    @Test public void representationOfCharacter() throws Exception {
        assertEquals("s:1:\"X\";", representationOf(new Character('X')));
    }

    @Test public void representationOfUmlautCharPrimitive() throws Exception {
        assertEquals("s:1:\"Ü\";", representationOf('Ü'));
    }

    @Test public void representationOfUmlautCharacter() throws Exception {
        assertEquals("s:1:\"Ü\";", representationOf(new Character('Ü')));
    }

    @Test public void representationOfBytePrimitive() throws Exception {
        assertEquals("i:16;", representationOf((byte)16));
        assertEquals("i:-16;", representationOf((byte)-16));
    }

    @Test public void representationOfBytes() throws Exception {
        assertEquals("i:16;", representationOf(new Byte((byte)16)));
        assertEquals("i:-16;", representationOf(new Byte((byte)-16)));
    }

    @Test public void representationOfShortPrimitive() throws Exception {
        assertEquals("i:1214;", representationOf((short)1214));
        assertEquals("i:-1214;", representationOf((short)-1214));
    }

    @Test public void representationOfShorts() throws Exception {
        assertEquals("i:1214;", representationOf(new Short((short)1214)));
        assertEquals("i:-1214;", representationOf(new Short((short)-1214)));
    }

    @Test public void representationOfIntPrimitive() throws Exception {
        assertEquals("i:6100;", representationOf(6100));
        assertEquals("i:-6100;", representationOf(-6100));
    }

    @Test public void representationOfIntegers() throws Exception {
        assertEquals("i:6100;", representationOf(new Integer(6100)));
        assertEquals("i:-6100;", representationOf(new Integer(-6100)));
    }

    @Test public void representationOfLongPrimitive() throws Exception {
        assertEquals("i:6100;", representationOf(6100L));
        assertEquals("i:-6100;", representationOf(-6100L));
    }

    @Test public void representationOfLongs() throws Exception {
        assertEquals("i:6100;", representationOf(new Long(6100L)));
        assertEquals("i:-6100;", representationOf(new Long(-6100L)));
    }

    @Test public void representationOfDoublePrimitive() throws Exception {
        assertEquals("d:0.1;", representationOf(0.1));
        assertEquals("d:-0.1;", representationOf(-0.1));
    }

    @Test public void representationOfDoubles() throws Exception {
        assertEquals("d:0.1;", representationOf(new Double(0.1)));
        assertEquals("d:-0.1;", representationOf(new Double(-0.1)));
    }

    @Test public void representationOfFloatPrimitive() throws Exception {
        assertEquals("d:0.1;", representationOf(0.1f));
        assertEquals("d:-0.1;", representationOf(-0.1f));
    }

    @Test public void representationOfFloats() throws Exception {
        assertEquals("d:0.1;", representationOf(new Float(0.1f)));
        assertEquals("d:-0.1;", representationOf(new Float(-0.1f)));
    }
    
    @Test public void representationOfBooleanPrimitive() throws Exception {
        assertEquals("b:1;", representationOf(true));
        assertEquals("b:0;", representationOf(false));
    }

    @Test public void representationOfBooleans() throws Exception {
        assertEquals("b:1;", representationOf(new Boolean(true)));
        assertEquals("b:0;", representationOf(new Boolean(false)));
    }

    @Test public void representationOfValueObject() throws Exception {
        assertEquals(
            "O:37:\"net.xp_framework.easc.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}", 
            representationOf(new Person())
        );
    }

    @Test public void representationOfStringHashMap() throws Exception {
        HashMap<String, String> h= new HashMap<String, String>();
        h.put("key", "value");
        h.put("number", "6100");
        
        assertEquals(
            "a:2:{s:3:\"key\";s:5:\"value\";s:6:\"number\";s:4:\"6100\";}", 
            representationOf(h)
        );
    }

    @Test public void representationOfMixedHashMap() throws Exception {
        HashMap<String, Object> h= new HashMap<String, Object>();
        h.put("key", "value");
        h.put("number", 6100);
        
        assertEquals(
            "a:2:{s:3:\"key\";s:5:\"value\";s:6:\"number\";i:6100;}", 
            representationOf(h)
        );
    }
    
    @Test public void representationOfStringArray() throws Exception {
        assertEquals(
            "a:2:{i:0;s:5:\"First\";i:1;s:6:\"Second\";}", 
            representationOf(new String[] { "First", "Second" })
        );
    }

    @Test public void representationOfIntegerPrimitiveArray() throws Exception {
        assertEquals(
            "a:2:{i:0;i:3;i:1;i:4;}", 
            representationOf(new int[] {3, 4})
        );
    }
    
    @Test public void representationOfDate() throws Exception {
        assertEquals(
            "D:1122369782;",
            representationOf(new Date(1122369782000L))
        );
    }
    
    @Test public void representationOfUserType() throws Exception {
        registerMapping(UUID.class, new Invokeable<String, UUID>() {
            public String invoke(UUID u) throws Exception {
                return "U:" + u + ";";
            }
        });
        
        assertEquals(
            "U:f0563880-3880-1056-9c5b-98814601ad8f;", 
            representationOf(UUID.fromString("f0563880-3880-1056-9c5b-98814601ad8f")
        ));
    }
    
    @Test public void valueOfNull() throws Exception {
        assertEquals(null, valueOf("N;"));
    }

    @Test public void valueOfBoolean() throws Exception {
        assertEquals(true, valueOf("b:1;"));
        assertEquals(false, valueOf("b:0;"));
    }

    @Test public void valueOfLong() throws Exception {
        assertEquals(6100L, (Long)valueOf("i:6100;"));
        assertEquals(-6100L, (Long)valueOf("i:-6100;"));
    }

    @Test public void valueOfDouble() throws Exception {
        assertEquals(0.1, (Double)valueOf("f:0.1;"), 0.0001);
        assertEquals(-0.1, (Double)valueOf("f:-0.1;"), 0.0001);
    }

    @Test public void valueOfString() throws Exception {
        assertEquals("Hello", valueOf("s:5:\"Hello\";"));
    }

    @Test public void valueOfQuotedString() throws Exception {
        assertEquals("\"Hello\", he said.", valueOf("s:17:\"\"Hello\", he said.\";"));
    }

    @Test
    public void valueOfIntegerArray() throws Exception {
        assertEquals("{1=4, 0=3}", ((HashMap)valueOf("a:2:{i:0;i:3;i:1;i:4;}")).toString());
    }

    @Test
    public void valueOfStringArray() throws Exception {
        assertEquals(
            "{1=More, 0=Binford}", 
            ((HashMap)valueOf("a:2:{i:0;s:7:\"Binford\";i:1;s:4:\"More\";}")).toString()
        );
    }
    
    @Test
    public void valueOfPersonObject() throws Exception {
        assertEquals(
            new Person(), 
            valueOf("O:37:\"net.xp_framework.easc.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}")
        );
    }

    @Test
    public void valueOfDate() throws Exception {
        assertEquals(
            new Date(1122369782000L), 
            valueOf("D:1122369782;")
        );
    }
}

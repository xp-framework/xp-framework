/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Ignore;
import net.xp_framework.unittest.Person;
import net.xp_framework.unittest.Employee;
import net.xp_framework.unittest.Item;
import net.xp_framework.unittest.ITest;
import net.xp_framework.unittest.NullInvocationHandler;
import net.xp_framework.easc.protocol.standard.Invokeable;
import net.xp_framework.easc.protocol.standard.MethodTarget;
import net.xp_framework.easc.reflect.*;
import java.util.HashMap;
import java.util.Date;
import java.util.UUID;
import java.util.Arrays;
import java.util.ArrayList;
import java.lang.reflect.Proxy;
import java.sql.Timestamp;
import java.security.Principal;
import javax.ejb.EJBHome;

import static org.junit.Assert.*;
import static net.xp_framework.easc.protocol.standard.Serializer.invokeableFor;
import static net.xp_framework.easc.protocol.standard.Serializer.representationOf;
import static net.xp_framework.easc.protocol.standard.Serializer.valueOf;
import static net.xp_framework.easc.protocol.standard.Serializer.registerMapping;
import static net.xp_framework.easc.protocol.standard.Serializer.unregisterMapping;

/**
 * Test the serialization / deserialization functionality
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.Serializer
 */
public class SerializerTest {

    /**
     * Tests serialization of strings
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfString() throws Exception {
        assertEquals("s:11:\"Hello World\";", representationOf("Hello World"));
    }

    /**
     * Tests serialization of the char primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfCharPrimitive() throws Exception {
        assertEquals("s:1:\"X\";", representationOf('X'));
    }

    /**
     * Tests serialization of Character primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfCharacter() throws Exception {
        assertEquals("s:1:\"X\";", representationOf(new Character('X')));
    }

    /**
     * Tests serialization of char primitive containing a German umlaut
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfUmlautCharPrimitive() throws Exception {
        assertEquals("s:1:\"\u00DC\";", representationOf('\u00DC'));
    }

    /**
     * Tests serialization of Character primitive wrapper containing a German umlaut
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfUmlautCharacter() throws Exception {
        assertEquals("s:1:\"\u00DC\";", representationOf(new Character('\u00DC')));
    }

    /**
     * Tests serialization of the byte primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfBytePrimitive() throws Exception {
        assertEquals("B:16;", representationOf((byte)16));
        assertEquals("B:-16;", representationOf((byte)-16));
    }


    /**
     * Tests serialization of the Byte primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfBytes() throws Exception {
        assertEquals("B:16;", representationOf(new Byte((byte)16)));
        assertEquals("B:-16;", representationOf(new Byte((byte)-16)));
    }

    /**
     * Tests serialization of the short primtive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfShortPrimitive() throws Exception {
        assertEquals("S:1214;", representationOf((short)1214));
        assertEquals("S:-1214;", representationOf((short)-1214));
    }

    /**
     * Tests serialization of the Short primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfShorts() throws Exception {
        assertEquals("S:1214;", representationOf(new Short((short)1214)));
        assertEquals("S:-1214;", representationOf(new Short((short)-1214)));
    }

    /**
     * Tests serialization of the int primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfIntPrimitive() throws Exception {
        assertEquals("i:6100;", representationOf(6100));
        assertEquals("i:-6100;", representationOf(-6100));
    }

    /**
     * Tests serialization of the Integer primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfIntegers() throws Exception {
        assertEquals("i:6100;", representationOf(new Integer(6100)));
        assertEquals("i:-6100;", representationOf(new Integer(-6100)));
    }

    /**
     * Tests serialization of the long primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfLongPrimitive() throws Exception {
        assertEquals("l:6100;", representationOf(6100L));
        assertEquals("l:-6100;", representationOf(-6100L));
    }

    /**
     * Tests serialization of the Long primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfLongs() throws Exception {
        assertEquals("l:6100;", representationOf(new Long(6100L)));
        assertEquals("l:-6100;", representationOf(new Long(-6100L)));
    }

    /**
     * Tests serialization of the double primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfDoublePrimitive() throws Exception {
        assertEquals("d:0.1;", representationOf(0.1));
        assertEquals("d:-0.1;", representationOf(-0.1));
    }

    /**
     * Tests serialization of the Double primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfDoubles() throws Exception {
        assertEquals("d:0.1;", representationOf(new Double(0.1)));
        assertEquals("d:-0.1;", representationOf(new Double(-0.1)));
    }

    /**
     * Tests serialization of the float primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfFloatPrimitive() throws Exception {
        assertEquals("f:0.1;", representationOf(0.1f));
        assertEquals("f:-0.1;", representationOf(-0.1f));
    }

    /**
     * Tests serialization of the Float primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfFloats() throws Exception {
        assertEquals("f:0.1;", representationOf(new Float(0.1f)));
        assertEquals("f:-0.1;", representationOf(new Float(-0.1f)));
    }
    
    /**
     * Tests serialization of the boolean primitive
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfBooleanPrimitive() throws Exception {
        assertEquals("b:1;", representationOf(true));
        assertEquals("b:0;", representationOf(false));
    }

    /**
     * Tests serialization of the Boolean primitive wrapper
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfBooleans() throws Exception {
        assertEquals("b:1;", representationOf(new Boolean(true)));
        assertEquals("b:0;", representationOf(new Boolean(false)));
    }

    /**
     * Tests serialization of a Person value object
     *
     * @see     net.xp_framework.unittest.Person
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfPersonValueObject() throws Exception {
        assertEquals(
            "O:32:\"net.xp_framework.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}", 
            representationOf(new Person())
        );
    }

    /**
     * Tests serialization of an Employee value object
     *
     * @see     net.xp_framework.unittest.Employee
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfEmployeeValueObject() throws Exception {
        assertEquals(
            "O:34:\"net.xp_framework.unittest.Employee\":3:{s:15:\"personellNumber\";i:1375;s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}", 
            representationOf(new Employee(1375))
        );
    }

    /**
     * Tests serialization of a java.util.HashMap<String, String>
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfStringHashMap() throws Exception {
        HashMap<String, String> h= new HashMap<String, String>();
        h.put("key", "value");
        h.put("number", "6100");
        h.put("null", null);

        // Use complicated way of checking this, the serialization order may
        // not be the same order we put the keys into the hashmap.
        String r= representationOf(h);
        assertTrue(r.startsWith("a:3:{"));
        assertTrue(r.endsWith("}"));
        assertTrue(r.contains("s:3:\"key\";s:5:\"value\";"));
        assertTrue(r.contains("s:4:\"null\";N;"));
        assertTrue(r.contains("s:6:\"number\";s:4:\"6100\";"));
    }

    /**
     * Tests serialization of a java.util.HashMap<String, Object> which contains
     * a string for the first key and an integer for the second
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfMixedHashMap() throws Exception {
        HashMap<String, Object> h= new HashMap<String, Object>();
        h.put("key", "value");
        h.put("number", 6100);

        String r= representationOf(h);
        assertTrue(r.startsWith("a:2:{"));
        assertTrue(r.endsWith("}"));
        assertTrue(r.contains("s:3:\"key\";s:5:\"value\";"));
        assertTrue(r.contains("s:6:\"number\";i:6100;"));
    }
    
    /**
     * Tests serialization of an array of strings
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfStringArray() throws Exception {
        assertEquals(
            "A:3:{s:5:\"First\";s:6:\"Second\";N;}", 
            representationOf(new String[] { "First", "Second", null })
        );
    }

    /**
     * Tests serialization of an array of integer primitives
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfIntegerPrimitiveArray() throws Exception {
        assertEquals(
            "A:2:{i:3;i:4;}", 
            representationOf(new int[] {3, 4})
        );
    }
    
    /**
     * Tests serialization of a java.util.Date
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfDate() throws Exception {
        assertEquals(
            "T:1122369782;",
            representationOf(new Date(1122369782000L))
        );
    }
    
    /**
     * Tests serialization of a "user type"
     *
     * @see     net.xp_framework.easc.protocol.standard.Serializer#registerMapping
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfUserType() throws Exception {
        registerMapping(UUID.class, new Invokeable<String, UUID>() {
            public String invoke(UUID u, Object arg) throws Exception {
                return "U:" + u + ";";
            }
        });
        
        assertEquals(
            "U:f0563880-3880-1056-9c5b-98814601ad8f;", 
            representationOf(UUID.fromString("f0563880-3880-1056-9c5b-98814601ad8f")
        ));
    }

    /**
     * Tests serialization of a NPE (NullPointerException)
     *
     * @throws  java.lang.Exception
     */
    @Test public void representationOfNPE() throws Exception {
        String serialized= representationOf(new NullPointerException());

        assertEquals(
            "e:11:\"NullPointer\":3:{s:7:\"message\";N;s:5:\"trace\";a:",
            serialized.substring(0, 52)
        );
        int offset= serialized.indexOf(':', 52)+ 2;
        assertEquals(
            "i:0;t:4:{s:4:\"file\";s:19:\"SerializerTest.java\";s:5:\"class\";s:40:\"net.xp_framework.unittest.SerializerTest\";s:6:\"method\";s:19:\"representationOfNPE\";s:4:\"line\";i:",
            serialized.substring(offset, offset+ 160)
        );
    }

    /**
     * Tests serialization of a generic Exception object
     *
     * @throws  java.lang.Exception
     */
    @Test public void representationOfException() throws Exception {
        String serialized= representationOf(new Exception());

        assertEquals(
            "E:19:\"java.lang.Exception\":3:{s:7:\"message\";N;s:5:\"trace\";a:",
            serialized.substring(0, 60)
        );
        int offset= serialized.indexOf(':', 60)+ 2;
        assertEquals(
            "i:0;t:4:{s:4:\"file\";s:19:\"SerializerTest.java\";s:5:\"class\";s:40:\"net.xp_framework.unittest.SerializerTest\";s:6:\"method\";s:25:\"representationOfException\";s:4:\"line\";i:",
            serialized.substring(offset, offset+ 166)
        );
    }    
    
    /**
     * Tests serialization of null
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfNull() throws Exception {
        Object o= null;
        assertEquals("N;", representationOf(o));
    }
    
    /**
     * Tests serialization of a java.util.ArrayList
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfArrayList() throws Exception {
        ArrayList<String> a= new ArrayList<String>();

        a.add("Binford");
        a.add("Tools");

        assertEquals(
            "A:2:{s:7:\"Binford\";s:5:\"Tools\";}", 
            representationOf(a)
        );
    }
    
    /**
     * Tests serialization of a java.util.ArrayList consisting of multiple
     * different types (String, Long, Timestamp).
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfMixedfArrayList() throws Exception {
        ArrayList<Object> a= new ArrayList<Object>();

        a.add("Binford");
        a.add(6100);
        a.add(new Timestamp(1122369782000L));

        assertEquals(
            "A:3:{s:7:\"Binford\";i:6100;T:1122369782;}", 
            representationOf(a)
        );
    }
    
    /**
     * Tests serialization of a enums
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfEnums() throws Exception {
        assertEquals(
            "O:56:\"net.xp_framework.easc.reflect.TransactionTypeDescription\":1:{s:4:\"name\";s:7:\"UNKNOWN\";}",
            representationOf(TransactionTypeDescription.UNKNOWN)
        );
    }

    /**
     * Tests serialization of a java.util.ArrayList of ArrayLists
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfNestedArrayList() throws Exception {
        ArrayList<ArrayList<String>> n= new ArrayList<ArrayList<String>>();
        ArrayList<String> a1= new ArrayList<String>();
        ArrayList<String> a2= new ArrayList<String>();

        a1.add("Binford");
        a2.add("Tools");
        n.add(a1);
        n.add(a2);

        assertEquals(
            "A:2:{A:1:{s:7:\"Binford\";}A:1:{s:5:\"Tools\";}}", 
            representationOf(n)
        );
    }

    /**
     * Tests serialization of bean description objects
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfBeanDescription() throws Exception {
        BeanDescription description= new BeanDescription();
        description.setJndiName("xp/test/Bean");
        
        assertEquals(
            "O:45:\"net.xp_framework.easc.reflect.BeanDescription\":2:{s:8:\"jndiName\";s:12:\"xp/test/Bean\";s:10:\"interfaces\";A:2:{N;N;}}",
            representationOf(description)
        );
    }

    /**
     * Tests serialization of interface description objects
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfInterfaceDescription() throws Exception {
        InterfaceDescription description= new InterfaceDescription();
        description.setClassName("net.xp_framework.beans.BeanHome");
        
        assertEquals(
            "O:50:\"net.xp_framework.easc.reflect.InterfaceDescription\":2:{s:9:\"className\";s:31:\"net.xp_framework.beans.BeanHome\";s:7:\"methods\";A:0:{}}",
            representationOf(description)
        );
    }

    /**
     * Tests serialization of method description objects
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfMethodDescription() throws Exception {
        MethodDescription description= new MethodDescription();
        description.setName("helloWorld");
        description.setReturnType(String.class);
        description.addRole(new Principal() {
            public boolean equals(Object cmp) { return false; }
            public int hashCode() { return 1; }
            public String getName() { return "mock"; }
            public String toString() { return "MockPrincipal"; }
        });
        description.setTransactionType(TransactionTypeDescription.UNKNOWN);
        
        assertEquals(
            "O:47:\"net.xp_framework.easc.reflect.MethodDescription\":5:{s:4:\"name\";s:10:\"helloWorld\";s:10:\"returnType\";c:s;s:14:\"parameterTypes\";A:0:{}s:5:\"roles\";A:1:{s:4:\"mock\";}s:15:\"transactionType\";O:56:\"net.xp_framework.easc.reflect.TransactionTypeDescription\":1:{s:4:\"name\";s:7:\"UNKNOWN\";}}",
            representationOf(description)
        );
    }

    /**
     * Tests serialization of the Person class (not the object)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfPersonClass() throws Exception {
        assertEquals(
            "C:32:\"net.xp_framework.unittest.Person\";",
            representationOf(Person.class)
        );
    }

    /**
     * Tests serialization of the String class
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfStringClass() throws Exception {
        assertEquals(
            "c:s;",
            representationOf(String.class)
        );
    }

    /**
     * Tests serialization of the ITest interface
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfInterface() throws Exception {
        assertEquals(
            "C:31:\"net.xp_framework.unittest.ITest\";",
            representationOf(ITest.class)
        );
    }

    /**
     * Tests serialization of the ITest interface
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void representationOfInterfaceWithSuperInterfaceRegistered() throws Exception {
        registerMapping(EJBHome.class, new Invokeable<String, EJBHome>() {
            public String invoke(EJBHome p, Object arg) throws Exception {
                Class ejbInterface= null;
                for (Class iface: p.getClass().getInterfaces()) {
                    if (EJBHome.class.isAssignableFrom(iface)) {
                        ejbInterface= iface;
                        break;
                    }
                }

                return "I:" + p.hashCode() + ":{" + representationOf(ejbInterface.getName()) + "}";
            }
        });

        assertEquals(
            "C:31:\"net.xp_framework.unittest.ITest\";",
            representationOf(ITest.class)
        );
        
        unregisterMapping(EJBHome.class);
    }

    /**
     * Tests deserialization of null (identified by "N" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfNull() throws Exception {
        assertNull(valueOf("N;"));
    }

    /**
     * Tests deserialization of booleans (identified by "b" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfBoolean() throws Exception {
        assertEquals(true, valueOf("b:1;"));
        assertEquals(false, valueOf("b:0;"));
    }

    /**
     * Tests deserialization of integers (identified by "i" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfInteger() throws Exception {
        assertEquals(Integer.valueOf(6100), (Integer)valueOf("i:6100;"));
        assertEquals(Integer.valueOf(-6100), (Integer)valueOf("i:-6100;"));
    }

    /**
     * Tests deserialization of longs (identified by "d" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfLong() throws Exception {
        assertEquals(Long.valueOf(6100L), (Long)valueOf("l:6100;"));
        assertEquals(Long.valueOf(-6100L), (Long)valueOf("l:-6100;"));
    }

    /**
     * Tests deserialization of floats (identified by "f" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfFloat() throws Exception {
        assertEquals(Float.valueOf(0.1f), (Float)valueOf("f:0.1;"), 0.0001);
        assertEquals(Float.valueOf(-0.1f), (Float)valueOf("f:-0.1;"), 0.0001);
    }

    /**
     * Tests deserialization of doubles (identified by "d" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfDouble() throws Exception {
        assertEquals(Double.valueOf(0.1d), (Double)valueOf("d:0.1;"), 0.0001);
        assertEquals(Double.valueOf(-0.1d), (Double)valueOf("d:-0.1;"), 0.0001);
    }

    /**
     * Tests deserialization of strings (identified by "s" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfString() throws Exception {
        assertEquals("Hello", valueOf("s:5:\"Hello\";"));
    }

    /**
     * Tests deserialization of strings with quotation marks
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfQuotedString() throws Exception {
        assertEquals("\"Hello\", he said.", valueOf("s:17:\"\"Hello\", he said.\";"));
    }

    /**
     * Tests deserialization of an array (identified by "a" token) 
     * consisting of integers
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfIntegerArray() throws Exception {
        HashMap h= (HashMap)valueOf("a:2:{i:0;i:3;i:1;i:4;}");
        assertEquals(3, h.get(0));
        assertEquals(4, h.get(1));
    }

    /**
     * Tests deserialization of an array of consisting of strings
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfStringArray() throws Exception {
        HashMap h= (HashMap)valueOf("a:2:{i:0;s:7:\"Binford\";i:1;s:4:\"More\";}");
        assertEquals("Binford", h.get(0));
        assertEquals("More", h.get(1));
    }
    
    /**
     * Tests deserialization an object (identified by "O" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     * @see     net.xp_framework.unittest.Person
     */
    @Test public void valueOfPersonObject() throws Exception {
        assertEquals(
            new Person(), 
            valueOf("O:32:\"net.xp_framework.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}")
        );
    }

    /**
     * Tests deserialization an object (identified by "O" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     * @see     net.xp_framework.unittest.Employee
     */
    @Test public void valueOfEmployeeObject() throws Exception {
        assertEquals(
            new Employee(1375), 
            valueOf("O:34:\"net.xp_framework.unittest.Employee\":3:{s:15:\"personellNumber\";i:1375;s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}")
        );
    }

    /**
     * Tests deserialization of a date (identified by "D" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfDate() throws Exception {
        assertEquals(
            new Date(1122369782000L), 
            valueOf("T:1122369782;")
        );
    }

    /**
     * Tests deserialization of a date (identified by "D" token) given a
     * java.sql.Timestamp object
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfTimestamp() throws Exception {
        assertEquals(
            new Timestamp(1122369782000L), 
            valueOf("T:1122369782;", Timestamp.class)
        );
    }

    /**
     * Tests deserialization of a date (identified by "D" token) given a
     * java.sql.Date object
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfNullSqlDate() throws Exception {
        java.sql.Date nullDate= null;

        assertEquals(
            nullDate, 
            valueOf("N;", java.sql.Date.class)
        );
    }

    /**
     * Tests deserialization of an Item value object
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfItem() throws Exception {
        assertEquals(
            new Item(), 
            valueOf("O:30:\"net.xp_framework.unittest.Item\":2:{s:2:\"id\";i:6100;s:9:\"createdAt\";T:1122369782;}")
        );
    }
    
    /**
     * Tests deserialization of strictly numeric arrays (identified by "A" token)
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfArray() throws Exception {
        Object[] result= (Object[])valueOf("A:2:{O:32:\"net.xp_framework.unittest.Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}s:5:\"World\";}");
        assertEquals(new Object[] { new Person(), new String("World") }, result);
    }

    /**
     * Tests deserialization of an object that has a readresolve method
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfObjectWithReadResolve() throws Exception {
        Object result= valueOf("O:38:\"net.xp_framework.unittest.EMailAddress\":1:{s:4:\"data\";s:15:\"foo@example.com\";}");
        assertEquals(
            new EMailAddress("foo", "example.com"), 
            result
        );
    }

    /**
     * Tests deserialization of an enum
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void valueOfOEnum() throws Exception {
        Object result= valueOf("O:56:\"net.xp_framework.easc.reflect.TransactionTypeDescription\":1:{s:4:\"name\";s:7:\"UNKNOWN\";}");
        assertEquals(
            TransactionTypeDescription.UNKNOWN,
            result
        );
    }
}

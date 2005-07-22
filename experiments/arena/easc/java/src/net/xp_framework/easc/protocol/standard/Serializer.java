/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

import java.lang.Integer;
import java.lang.Long;
import java.lang.Integer;
import java.lang.Float;
import java.lang.Double;
import java.lang.Boolean;
import java.lang.reflect.Field;
import java.lang.reflect.Modifier;
import java.lang.NullPointerException;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;

/**
 * Serializer / unserializer for PHP serialized data
 *
 * Usage example:
 * <code>
 *   Object o= Serializer.unserialize("s:11:\"Hello World\";");
 *   System.out.println(o);
 * </code>
 *
 * Usage example:
 * <code>
 *   String s= Serializer.serialize("Hello");
 *   System.out.println(s);
 * </code>
 *
 * @see   http://php.net/unserialize
 * @see   http://php.net/serialize
 */
public class Serializer {
}

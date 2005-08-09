/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.util.HashMap;

/**
 * Message type enumeration
 *
 */
public enum MessageType {
    Initialize, Status, Lookup, Call, Value, Finalize;

    private static HashMap<Integer, MessageType> map= new HashMap<Integer, MessageType>(); 

    static {
        for (MessageType t : MessageType.values()) {
            map.put(t.ordinal(), t);
        }
    }
    
    /**
     * Get a type for a given message type identifier
     *
     * @static
     * @access  public
     * @param   int identifier the integer number passed on the wire
     * @return  net.xp_framework.easc.protocol.standard.MessageType
     */
    public static MessageType valueOf(int identifier) {
        return map.get(identifier);
    }
}

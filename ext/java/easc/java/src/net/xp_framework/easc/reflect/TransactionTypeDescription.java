/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import java.io.Serializable;

/**
 * Describes an EJB transaction type
 *
 */
public enum TransactionTypeDescription implements Serializable {
    NOT_SUPPORTED,
    REQUIRED,
    SUPPORTS,
    REQUIRES_NEW,
    MANDATORY,
    NEVER,
    UNKNOWN;
}

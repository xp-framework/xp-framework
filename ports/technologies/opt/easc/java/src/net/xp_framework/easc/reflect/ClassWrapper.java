/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.reflect;

import java.io.Serializable;

/**
 * Wrapper type for Java classes
 *
 */
public class ClassWrapper implements Serializable {
    public transient Class referencedClass;

    public ClassWrapper(Class c) {
        this.referencedClass= c;
    }
}

/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

package net.xp_framework.ejb_gen;

import net.xp_framework.ejb_gen.ViewType;
import java.lang.annotation.*;

import static java.lang.annotation.RetentionPolicy.*;
import static java.lang.annotation.ElementType.*;

@Target(METHOD) @Retention(SOURCE)
public @interface InterfaceMethod {
    ViewType[] viewTypes() default {};
}

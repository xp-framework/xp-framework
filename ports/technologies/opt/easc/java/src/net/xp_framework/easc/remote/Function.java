package net.xp_framework.easc.remote;

import java.lang.reflect.Method;
import java.lang.reflect.InvocationTargetException;

/**
 * Function class - closure-like
 *
 */
public class Function<R> {
    protected Method delegate;
    
    /**
     * Constructor
     *
     */
    public Function(Class<?> c, String name, Class<?>... signature) throws IllegalArgumentException {
        this.setDelegate(c, name, signature);
    }
    
    /**
     * Constructor
     *
     */
    public Function(Class<?>... signature) throws IllegalArgumentException {
        this.setDelegate(this.getClass(), "apply", signature);
    }

    /**
     * No-arg constructor so this is overwriteable
     *
     */
    protected Function() {
    }
    
    /**
     * Set delegate to invoke when apply() is called
     *
     */
    protected void setDelegate(Class<?> c, String name, Class<?>... signature) {
        try {
            this.delegate = c.getDeclaredMethod(name, signature);
        } catch (NoSuchMethodException e) {
            throw new IllegalArgumentException(e);
        }
    }

    /**
     * Delegator
     *
     * @throws  RuntimeException in case invocation results in an exception being thrown
     */
    R apply(Object... args) {
        try {
            @SuppressWarnings("unchecked")
            R r= (R) this.delegate.invoke(this, args);
            return r;
        } catch (InvocationTargetException e) {
            throw new RuntimeException(e.getCause());
        } catch (IllegalAccessException e) {
            throw new RuntimeException(e);
        }
    }
    
    /**
     * Creates a string representation of this function
     *
     */
    @Override public String toString() {
        return this.getClass().getName() + "(->" + this.delegate.toString() + ")";
    }
}

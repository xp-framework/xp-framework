package net.xp_framework.easc.remote;

/**
 * Function class - closure-like
 *
 */
public abstract class Functions {

    static class NullFunction extends Function<Boolean> {
        private boolean is;

        public NullFunction(boolean is) {
            super(Object.class);
            this.is= is;
        }

        public boolean apply(Object arg) {
            return this.is ? null == arg : null != arg;
        }
        
        @Override public String toString() {
            return "Function<arg is " + (this.is ? "" : "not ") + "null>";
        }
    }
    
    protected static final Function<Boolean> NOT_NULL= new NullFunction(false);
    protected static final Function<Boolean> IS_NULL= new NullFunction(true);
    
    /**
     * Function that returns whether an object is null
     *
     */
    public static Function<Boolean> isNull() {
        return IS_NULL;
    }

    /**
     * Function that returns whether an object is null
     *
     */
    public static Function<Boolean> notNull() {
        return NOT_NULL;
    }

    static class ConstructFunction<T> extends Function<T> {
        private java.lang.reflect.Constructor<T> constructor;
        
        public ConstructFunction(Class<T> t, Class<?>[] signature) throws IllegalArgumentException {
            try {
                this.constructor = t.getConstructor(signature);
            } catch (NoSuchMethodException e) {
                throw new IllegalArgumentException(e);
            }
        }
    
        @Override public T apply(Object... args) {
            try {
                return this.constructor.newInstance(args);
            } catch (Exception e) {
                throw new RuntimeException(e);
            }
        }
    }
    
    /**
     * Function that creates objects from input
     *
     */
    public static <T> Function<T> construct(Class<T> t, Class<?>... signature) {
        return new ConstructFunction<T>(t, signature);
    }
    
    /**
     * Function that delegates to a method
     *
     */
    public static <T> Function<T> method(Class<T> returnType, Class<?> c, String name, Class<?>... signature) {
        return new Function<T>(c, name, signature);
    }

    static class CallFunction<T> extends Function<T> {
        private Object[] args;
        
        public CallFunction(Class<?> c, String name, Class<?>[] signature, Object[] args) {
            this.setDelegate(c, name, signature);
            this.args = args;
        }

        @Override public T apply(Object... args) {
            return super.apply(this.args);
        }
    }


    /**
     * Function that delegates to a function call
     *
     */
    public static <T> Function<Function<T>> call(Class<T> returnType, final Class<?> c, final String name, final Class<?>... signature) {
        return new Function<Function<T>>(Object[].class) {
            @Override public Function<T> apply(Object... args) {
                return new CallFunction<T>(c, name, signature, args);
            }
        };
    }
}

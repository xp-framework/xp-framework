package net.xp_framework.easc.remote;

/**
 * Utility class
 *
 */
public abstract class Try {

    public static <R> R these(Iterable<Function<R>> functions) throws Throwable {
        Throwable error = null;
        for (Function<R> function : functions) {
            try {
                return function.apply();
            } catch (Throwable e) {
                error = e;
                continue;
            }
        }
        throw null != error ? error : new IllegalStateException("No functions in functions");
    }

    public static <R> R these(Function<R>... functions) throws Throwable {
        return these(java.util.Arrays.asList(functions));
    }
}

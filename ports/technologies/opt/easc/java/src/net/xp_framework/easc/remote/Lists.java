package net.xp_framework.easc.remote;

import java.util.ArrayList;
import java.util.Collection;
import java.util.NoSuchElementException;
import java.util.Arrays;

/**
 * List extension class
 *
 */
public class Lists {

    /**
     * Executes a function for each element in the list
     *
     * <code>
     *   Lists.each(string.split(";"), new Function<Void>(String.class) {
     *      public void apply(String in) {
     *          System.out.println(in);
     *      }
     *   });
     * </code>
     */
    public static <T> void each(T[] objects, Function<Void> function) {
        each(Arrays.asList(objects), function);
    }

    public static <T> void each(Iterable<T> objects, Function<Void> function) {
        for (T object : objects) {
            function.apply(object);
        }
    }
    
    /**
     * Finds the first element for which the iterator returns true.
     *
     * <code>
     *   String[] words= new String[] { "This", "is", "a", "test" };
     *   String first= Lists.find(words, new Function<Boolean>(String.class) {
     *      public boolean apply(String in) {
     *          return in.length() <= 3;
     *      }
     *   });
     *
     *   // -> "is"
     * </code>
     */
    public static <T> T find(T[] objects, Function<Boolean> function) {
        return find(Arrays.asList(objects), function);
    }

    public static <T> T find(Iterable<T> objects, Function<Boolean> function) {
        for (T object : objects) {
            if (function.apply(object)) return object;
        }
        return null;
    }

    /**
     * Gets the first element for which the iterator returns true.
     * Similar to find except that an exception is thrown if no 
     * element can be found.
     *
     */
    public static <T> T get(T[] objects, Function<Boolean> function) {
        return get(Arrays.asList(objects), function);
    }

    public static <T> T get(Iterable<T> objects, Function<Boolean> function) {
        for (T object : objects) {
            if (function.apply(object)) return object;
        }
        throw new NoSuchElementException("Could not find any element matching " + function);
    }

    /**
     * Finds all elements for which the iterator returns true.
     *
     */
    public static <T> Iterable<T> findAll(T[] objects, Function<Boolean> function) {
        return findAll(Arrays.asList(objects), function);
    }

    public static <T> Iterable<T> findAll(Iterable<T> objects, Function<Boolean> function) {
        Collection<T> results= new ArrayList<T>();
        for (T object : objects) {
            if (function.apply(object)) results.add(object);
        }
        return results;
    }

    /**
     * Collects all values
     *
     */
    public static <T, R> Iterable<R> collect(T[] objects, Function<R> function) {
        return collect(Arrays.asList(objects), function);
    }

    public static <T, R> Iterable<R> collect(Iterable<T> objects, Function<R> function) {
        Collection<R> results= new ArrayList<R>();
        for (T object : objects) {
            results.add(function.apply(object));
        }
        return results;
    }
}

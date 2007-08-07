import java.util.List;
import java.util.ArrayList;

import static java.lang.System.*;

public class Profile {

    public static void main(String... args) throws Throwable {
        int times= args.length > 1 ? Integer.parseInt(args[1]) : 10000000;
        List<Enum<?>> profilees= new ArrayList<Enum<?>>();

        // Check whether ClassName::Member or just ClassName was given
        int pos= args[0].indexOf(':');
        if (pos > 0) {
            profilees.add(Enum.valueOf(
                (Class)Class.forName(args[0].substring(0, pos)),
                args[0].substring(pos+ 2, args[0].length())
            ));
        } else {
            for (Enum<?> e: (Enum<?>[])Class.forName(args[0]).getMethod("values").invoke(null)) {
                profilees.add(e);
            }
        }
        
        // Run!
        for (Enum<?> p: profilees) {
            long start= currentTimeMillis();
            ((Profileable)p).run(times);
            long stop= currentTimeMillis();
            double elapsed= ((double)stop- start) / 1000;
            
            out.printf(
                "%s: %.3f seconds for %d runs (%.0f / second)%n",
                p.name(),
                elapsed,
                times,
                times * (1 / elapsed)
            );
        }
    }
}

public enum Increment implements Profileable {
    POST {
        public void run(int times) {
            int a= 0;
            for (int i= 0; i < times; i++) {
                a++;
            }
        }
    },
    PRE {
        public void run(int times) {
            int a= 0;
            for (int i= 0; i < times; i++) {
                ++a;
            }
        }
    },
    BINARY {
        public void run(int times) {
            int a= 0;
            for (int i= 0; i < times; i++) {
                a= a+ 1;
            }
        }
    }
}

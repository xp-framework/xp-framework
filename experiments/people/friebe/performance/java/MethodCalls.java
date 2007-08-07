public enum MethodCalls implements Profileable {
    PUBLIC {
        public void publicMethod(int i) {
            i++;    // NOOP
        }
        
        public void run(int times) {
            for (int i= 0; i < times; i++) {
                this.publicMethod(i);
            }
        }
    },
    PROTECTED {
        protected void protectedMethod(int i) {
            i++;    // NOOP
        }
        
        public void run(int times) {
            for (int i= 0; i < times; i++) {
                this.protectedMethod(i);
            }
        }
    },
    PRIVATE {
        private void privateMethod(int i) {
            i++;    // NOOP
        }
        
        public void run(int times) {
            for (int i= 0; i < times; i++) {
                this.privateMethod(i);
            }
        }
    }
}

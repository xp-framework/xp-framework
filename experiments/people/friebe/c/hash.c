#include <stdio.h>

typedef struct {
    int key;
    char *value;
} hash;

static void printhash(char *name, hash *h)
{
    printf("%s = {\n", name);
    while (h->key != NULL) {
        printf("  %d: %s\n", h->key, h->value);
        h++;
    }
    printf("}\n");
}

int main(int argc, char **argv)
{
    hash a[] = {
        { 1, "Hello" },
        { 2, "World" },
        { NULL, NULL }
    };
    hash b[2];
    hash *c;
    
    printhash("a", a);
    
    b[0].key= 1;
    b[0].value= "Dynamic";
    b[1].key= NULL;
    b[1].value= NULL;
    printhash("b", b);
    
    c= (hash *) malloc(sizeof(hash) * 2);
    c[0].key= 1;
    c[0].value= "Pointer";
    c[1].key= NULL;
    c[1].value= NULL;
    printhash("c", c);
    free(c);
    
    return 0;
}

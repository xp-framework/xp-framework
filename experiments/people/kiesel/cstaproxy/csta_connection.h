typedef struct {
	int hClient;
	int hServer;
	
	int is_authenticated;
	char *username;
} proxy_connection;

extern int alloc_connection(proxy_connection **);
extern void init_connection(proxy_connection *);
extern void free_connection(proxy_connection **);
extern proxy_connection *get_connection_by_socket(proxy_connection *, int socket);

/**
 * Structure and functions for a client/server connection
 */
typedef struct {
	int hClient;
	int hServer;
	
	int is_authenticated;
	char *username;
	char *phone;
	
	int pluginmask;
	int *callback;
} proxy_connection;

extern int alloc_connection(proxy_connection **);
extern void init_connection(proxy_connection *);
extern void free_connection(proxy_connection **);
extern void pc_set_username (proxy_connection *, char*);
extern void pc_set_phone (proxy_connection *, char*);
extern void pc_set_callback (proxy_connection *, int *);

/**
 * Structure and functions for the administration
 */
typedef struct {
	int allocated;
	int count;
	
	proxy_connection **connections;
} connection_context;

extern proxy_connection *get_connection_by_socket(connection_context *, int socket);
extern void alloc_connection_context (connection_context **);
extern int add_connection(connection_context *, proxy_connection *);
extern int delete_connection(connection_context *, proxy_connection *);
extern void _dump_context(connection_context *);

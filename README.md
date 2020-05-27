# DB Cluster

 NDB API

   NDB API app access the NDB Cluster's data store directly, without requiring a MySQL Server as an intermediary. This means that such apps are not bound by the MySQL privilege system; any NDB API app has R/W access to any NDB table stored in the same NDB Cluster at any time without restriction. (sync replica & cluster of nodes to do extensions.)

   It is possible to distribute the MySQL grant tables, converting them from the default storage engine to NDB. Once this has been done, NDB API apps can access any of the MySQL grant tables. This means that such apps can r/w user names, passwords, and any other data stored in these tables.


* NDB scans = SQL cursors 

      table scans and row scans.

* error-handling 

       to provide a means of recovering from failed operations and other problems.
       
       
* cluster Initialize


      int main(int argc, char** argv)
      {
        if (argc != 3)
        {
          std::cout << "Arguments are <socket mysqld> <connect_string cluster>.\n";
          exit(-1);
        }
        char * mysqld_sock  = argv[1];
        const char *connection_string = argv[2];
        ndb_init();

        Ndb_cluster_connection *cluster_connection=
          new Ndb_cluster_connection(connection_string); // Object representing the cluster

        int r= cluster_connection->connect(5 /* retries               */,
               3 /* delay between retries */,
               1 /* verbose               */);
        if (r > 0)
        {
          std::cout
            << "Cluster connect failed, possibly resolved with more retries.\n";
          exit(-1);
        }
        else if (r < 0)
        {
          std::cout
            << "Cluster connect failed.\n";
          exit(-1);
        }

        if (cluster_connection->wait_until_ready(30,30))
        {
          std::cout << "Cluster was not ready within 30 secs." << std::endl;
          exit(-1);
        }
        // connect to mysql server
        MYSQL mysql;
        if ( !mysql_init(&mysql) ) {
          std::cout << "mysql_init failed\n";
          exit(-1);
        }
        if ( !mysql_real_connect(&mysql, "localhost", "root", "", "",
            0, mysqld_sock, 0) )
          MYSQLERROR(mysql);

        /********************************************
         * Connect to database via mysql-c          *
         ********************************************/
        mysql_query(&mysql, "CREATE DATABASE ndb_examples");
        if (mysql_query(&mysql, "USE ndb_examples") != 0) MYSQLERROR(mysql);
        create_table(mysql);

        Ndb* myNdb= new Ndb( cluster_connection,
               "ndb_examples" );  // Object representing the database

        if (myNdb->init() == -1) {
          APIERROR(myNdb->getNdbError());
          exit(-1);
        }

        const NdbDictionary::Dictionary* myDict= myNdb->getDictionary();
        const NdbDictionary::Table *myTable= myDict->getTable("api_retries");
        if (myTable == NULL)
        {
          APIERROR(myDict->getNdbError());
          return -1;
        }
        /************************************
         * Execute some insert transactions *
         ************************************/

        std::cout << "Ready to insert rows.  You will see notices for temporary "
          "errors, permenant errors, and retries. \n";
        for (int i = 10000; i < 20000; i++) {
          executeInsertTransaction(i, myNdb, myTable);
        }
        std::cout << "Done.\n";

        delete myNdb;
        delete cluster_connection;

        ndb_end(0);
        return 0;
      }


* Check Point

Local checkpoint (LCP).  This is a checkpoint that is specific to a single node; however, LCPs take place for all nodes in the cluster more or less concurrently. 

An LCP involves saving all of a node's data to disk, and so usually occurs every few minutes, depending upon the amount of data stored by the node.

More detailed information about LCPs and their behavior can be found in the MySQL Manual; see in particular Defining NDB Cluster Data Nodes.

Global checkpoint (GCP).  A GCP occurs every few seconds, when transactions for all nodes are synchronized and the REDO log is flushed to disk.

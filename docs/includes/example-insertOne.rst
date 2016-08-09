.. code-block:: php
                
   <?php
   
   $database = new MongoDB\Client;

   $collection = $database->selectCollection('users','restaurants');

   $newUser = $collection->insertOne(
       [
           'username' => 'admin',
           'email' => 'admin@example.com',
           'name' => 'Anna Bennett'
       ],
   );

   echo "<pre>";
   var_dump($newUser);

The output would resemble:

.. code-block:: none
                
   object(MongoDB\InsertOneResult)#13 (3) {
     ["writeResult":"MongoDB\InsertOneResult":private]=>
     object(MongoDB\Driver\WriteResult)#12 (9) {
       ["nInserted"]=>
       int(1)
       ["nMatched"]=>
       int(0)
       ["nModified"]=>
       int(0)
       ["nRemoved"]=>
       int(0)
       ["nUpserted"]=>
       int(0)
       ["upsertedIds"]=>
       array(0) {
       }
       ["writeErrors"]=>
       array(0) {
       }
       ["writeConcernError"]=>
       NULL
       ["writeConcern"]=>
       array(4) {
         ["w"]=>
         NULL
         ["wmajority"]=>
         bool(false)
         ["wtimeout"]=>
         int(0)
         ["journal"]=>
         NULL
       }
     }
     ["insertedId":"MongoDB\InsertOneResult":private]=>
     object(MongoDB\BSON\ObjectID)#11 (1) {
       ["oid"]=>
       string(24) "577282631f417d1823121691"
     }
     ["isAcknowledged":"MongoDB\InsertOneResult":private]=>
     bool(true)
   }
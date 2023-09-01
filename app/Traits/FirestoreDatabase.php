<?php

namespace App\Traits;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\FirestoreClient;

trait FirestoreDatabase
{

    /**
     * @return FirestoreClient
     * @throws GoogleException
     */
    protected function initialize(): FirestoreClient
    {
        $config = [
            'keyFilePath' => base_path(env('PATH_FIRESTORE'))
        ];
        return new FirestoreClient($config);
    }

    protected function getReference(FirestoreClient $firestoreClient, $collection) : CollectionReference
    {
        return $firestoreClient->collection($collection);
    }

}

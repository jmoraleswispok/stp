<?php

namespace App\Firestore;

use App\Traits\FirestoreDatabase;
use Google\Cloud\Core\Exception\GoogleException;

class SiapaFirestore {
    use FirestoreDataBase;

    private \Google\Cloud\Firestore\FirestoreClient $firestore;
    private \Google\Cloud\Firestore\CollectionReference $reference;
    private $document;

    private $provider;

    /**
     * @throws GoogleException
     */
    public function __construct($provider)
    {
        $this->referent = $provider;
        $this->firestore = $this->initialize();
        $this->reference = $this->getReference($this->firestore, $provider);
    }

    /**
     * @return void
     */
    private function getDocument($referent)
    {
        $this->document = $this->reference->document($referent);
    }

    public function set($accouny, $idPaymeth, $status, $nombre, $paymenth, $message = ''): void
    {
        $this->getDocument($accouny);
        $this->document->set([
            'name' => $nombre
        ]);

        $this->getDocument("$accouny/payments/$idPaymeth");
        $this->document->delete();
        $this->document->set([
            'paymenth' => $paymenth,
            'status' => $status,
            'message' => $message
        ]);
    }

    public function update($accouny, $idPaymeth)
    {
        $this->getDocument("$accouny/payments/$idPaymeth");
    }

}

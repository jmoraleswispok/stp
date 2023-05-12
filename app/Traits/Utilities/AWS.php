<?php


namespace App\Traits\Utilities;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

trait AWS
{

    /**
     * @param string $key
     * @param string $expiry
     * @return string
     */
    public function getUri(string $key, string $expiry): string
    {
        $s3 = Storage::disk('s3');
        // $client = $s3->getDriver()->getAdapter()->getClient();
        $client = $s3->getClient();
        $bucket = Config::get('filesystems.disks.s3.bucket');
        $command = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        $request = $client->createPresignedRequest($command, $expiry);
        return (string) $request->getUri();
    }
}

<?php
require_once __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

$bucket = 'test';
$key = 'key';
$expires = '+8600 seconds';
$sharedConfig = [
    'region' => getenv('AWS_DEFAULT_REGION'),
    'version' => 'latest',
    'key' => 'test',
    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    'endpoint' => getenv('AWS_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'signature_version' => 'v4',
];

$sdk = new Aws\Sdk($sharedConfig);
$client = $sdk->createMultiRegionS3();

$multipartUpload = $client->createMultipartUpload([
    'Bucket' => $bucket,
    'Key' => $key,
]);

$uploadId = $multipartUpload->get('UploadId');

$command = $client->getCommand('UploadPart', [
    'Bucket' => $bucket,
    'Key' => $key,
    'UploadId' => $uploadId,
    'PartNumber' => '1',
]);

$presignedRequest = $client->createPresignedRequest($command, $expires);
$url = $presignedRequest->getUri();

print "Url: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url");
curl_exec($ch);
curl_close($ch);

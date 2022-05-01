<?php

namespace AppBundle\Service;

use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * This service is injected in twig
 * Class S3Uploader
 * @package AppBundle\Service
 */
class S3Uploader
{

    private $s3PrivateClient;
    private $privateBucketName;
    private $privateBucketDirectory;

    public function __construct(S3Client $s3PrivateClient, $privateBucketName, $privateBucketDirectory)
    {
        $this->s3PrivateClient = $s3PrivateClient;
        $this->privateBucketName = $privateBucketName;
        $this->privateBucketDirectory = $privateBucketDirectory;
    }


    /**
     * Get file from private bucket.
     *
     * @param string $filename
     *
     * @return \Aws\Result|bool
     */
    public function getFile($filename)
    {
        try {
            return $this->s3PrivateClient->getObject(
                [
                    'Bucket' => $this->privateBucketName,
                    'Key'    => $this->privateBucketDirectory.'/'.$filename,
                ]
            );
        } catch (S3Exception $e) {
            return false;
        }
    }

    /**
     * Delete file to private bucket
     *
     * @param string $filename
     */
    public function deleteFile($filename)
    {
        try {
            $this->s3PrivateClient->deleteObject(
                [
                    'Bucket' => $this->privateBucketName,
                    'Key'    => $this->privateBucketDirectory.'/'.$filename,
                ]
            );
        } catch (S3Exception $e) {
        }
    }


    /**
     * Upload file to private bucket
     *
     * @param string $filename
     * @param string $path
     *
     * @return \Aws\Result|bool
     */
    public function uploadFile($filename, $path)
    {
        try {
            if(file_exists($path)) {
                $file = $this->s3PrivateClient->putObject(array(
                    'Bucket' => $this->privateBucketName,
                    'Key' => $this->privateBucketDirectory . '/' . $filename,
                    'SourceFile' => $path,
                    'ACL'    => 'public-read'
                ));

                $this->s3PrivateClient->waitUntil('ObjectExists',array(
                    'Bucket' => $this->privateBucketName,
                    'Key' => $this->privateBucketDirectory . '/' . $filename,
                ));

                unlink($path);
                
                return $file;

            }
            return false;
        } catch (S3Exception $e) {
            return false;
        }
    }
    
}


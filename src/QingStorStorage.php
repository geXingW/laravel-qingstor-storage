<?php
/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2017/12/20 0020
 * Time: 17:34
 */

namespace GeXingW\QingStorStorage;


use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;

class QingStorStorage extends AbstractAdapter
{

    protected $bucketName;

    protected $client;

    protected $qingstorService;

    protected $bucket;

    public function __construct(QingstorClient $QingstorClient, $bucket = '', $zone = '')
    {
        $this->client = $QingstorClient;
        $this->bucketName = $bucket;
        $this->qingstorService = $QingstorClient->service;

        $this->bucket = $this->qingstorService->Bucket($bucket, $zone);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        try {
            $options = [
                'body' => $contents,
            ];
            $res = $this->bucket->putObject($path, $options);
            return $res->statusCode === 201 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        try {
            $options = [
                'body' => $resource,
            ];
            $res = $this->bucket->putObject($path, $options);
            return $res->statusCode === 201 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * Update a file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        try {
            $options = [
                'X-QS-Move-Source' => "/$this->bucketName/" . trim($path, '/')
            ];
            $res = $this->bucket->putObject($newpath, $options);
            return $res->statusCode ? true : false;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        try {
            $options = ['x-qs-copy-source' => $path];
            $res = $this->bucket->putObject($newpath, $options);
            return $res->statusCode === 201 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        try {
            $res = $this->bucket->deleteObject($path);
            return $res->statusCode === 204 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        //TODO Add delete directory..
        return false;
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        //TODO Add create directory..
        return false;
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        try {
            $res = $this->bucket->getObject($path);
            return $res->statusCode === 200 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        try {
            $res = $this->bucket->getObject($path);
            if ($res->statusCode !== 200) {
                return false;
            }
            return [
                "res" => $res->{'res'},
                "statusCode" => $res->{'statusCode'},
                "Date" => $res->{'Date'},
                "Content-Type" => $res->{'Content-Type'},
                "Content-Length" => $res->{'Content-Length'},
                "Connection" => $res->{'Connection'},
                "Etag" => $res->{'Etag'},
                "Last-Modified" => $res->{'Last-Modified'},
                "x-qs-request-id" => $res->{'x-qs-request-id'},
                "x-qs-storage-class" => $res->{'x-qs-storage-class'},
                "Server" => $res->{'Server'},
                "body" => $res->{'body'},
                "content" => $res
            ];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {

        try {
            $res = $this->bucket->getObject($path);
            if ($res->statusCode !== 200) {
                return false;
            }
            return ['stream' => $res->body];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        try {
            $res = $this->bucket->listObjects(['prefix' => $directory]);

            if ($res->statusCode !== 200) {
                return [];
            }

            $contents = [];
            foreach ($res->keys as $item) {
                $normalized = [
                    'type' => 'file',
                    'path' => $item['key'],
                    'timestamp' => $item['modified'],
                    'etag' => $item['etag'],
                ];

                if ($normalized['type'] === 'file') {
                    $normalized['size'] = $item['size'];
                }

                array_push($contents, $normalized);
            }
            return $contents;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        return $this->read($path);
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->read($path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        return $this->read($path);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->read($path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        return $this->read($path);
    }


}
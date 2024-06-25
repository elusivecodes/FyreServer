<?php
declare(strict_types=1);

namespace Fyre\Server;

use Fyre\FileSystem\File;
use Fyre\Server\Exceptions\ServerException;

use function fclose;
use function file_put_contents;
use function readfile;
use function stream_get_meta_data;
use function tmpfile;

/**
 * DownloadResponse
 */
class DownloadResponse extends ClientResponse
{
    protected File $file;

    /**
     * Create a DownloadResponse from binary data.
     *
     * @param string $data The file data.
     * @param array $options The response options.
     * @return DownloadResponse A new DownloadResponse.
     */
    public static function fromBinary(string $data, array $options = []): static
    {
        $tmpFile = tmpfile();
        $metaData = stream_get_meta_data($tmpFile);
        fclose($tmpFile);

        file_put_contents($metaData['uri'], $data);

        return new static($metaData['uri'], $options);
    }

    /**
     * New DownloadResponse constructor.
     *
     * @param string $path The file path.
     * @param array $options The response options.
     *
     * @throws ServerException if the file path is not valid.
     */
    public function __construct(string $path, array $options = [])
    {
        $this->file = new File($path);

        if (!$this->file->exists()) {
            throw ServerException::forMissingFile($this->file->path());
        }

        $options['filename'] ??= $this->file->baseName();
        $options['mimeType'] ??= $this->file->mimeType();

        $options['headers'] ??= [];
        $options['headers']['Content-Type'] ??= $options['mimeType'].'; charset=UTF-8';
        $options['headers']['Content-Disposition'] ??= 'attachment; filename="'.$options['filename'].'"';
        $options['headers']['Expires'] ??= '0';
        $options['headers']['Content-Transfer-Encoding'] ??= 'binary';
        $options['headers']['Content-Length'] ??= (string) $this->file->size();
        $options['headers']['Cache-Control'] ??= ['private', 'no-transform', 'no-store', 'must-revalidate'];

        parent::__construct($options);
    }

    /**
     * Get the download File.
     *
     * @return File The File.
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * Send the response to the client.
     */
    public function send(): void
    {
        parent::send();

        readfile($this->file->path());
    }

    /**
     * Set the message body.
     *
     * @param string $data The message body.
     *
     * @throws ServerException as body cannot be set for a DownloadResponse.
     */
    public function setBody(string $data): static
    {
        throw ServerException::forUnsupportedSetBody();
    }
}

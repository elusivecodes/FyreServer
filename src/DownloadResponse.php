<?php
declare(strict_types=1);

namespace Fyre\Server;

use
    Fyre\FileSystem\File,
    Fyre\Server\Exceptions\ServerException;

use function
    fclose,
    file_put_contents,
    readfile,
    stream_get_meta_data,
    tmpfile;

/**
 * DownloadResponse
 */
class DownloadResponse extends ClientResponse
{

    protected File $file;

    /**
     * Create a DownloadResponse from binary data.
     * @param string $data The file data.
     * @param string $filename The download file name.
     * @param string $mimeType The file MIME type.
     */
    public static function fromBinary(string $data, string $filename = null, string $mimeType = null)
    {
        $tmpFile = tmpfile();
        $metaData = stream_get_meta_data($tmpFile);
        fclose($tmpFile);

        $path = $metaData['uri'];

        file_put_contents($path, $data);

        return new static($path, $filename, $mimeType);
    }

    /**
     * New DownloadResponse constructor.
     * @param string $path The file path.
     * @param string $filename The download file name.
     * @param string $mimeType The file MIME type.
     */
    public function __construct(string $path, string $filename = null, string $mimeType = null)
    {
        parent::__construct();

        $this->file = new File($path);

        if (!$this->file->exists()) {
            throw ServerException::forMissingFile($this->file->path());
        }

        $filename ??= $this->file->baseName();
        $mimeType ??= $this->file->mimeType();
        $contentLength ??= $this->file->size();

        $this->setContentType($mimeType);
        $this->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $this->setHeader('Expires', '0');
        $this->setHeader('Content-Transfer-Encoding', 'binary');
        $this->setHeader('Content-Length', (string) $contentLength);
        $this->setHeader('Cache-Control', ['private', 'no-transform', 'no-store', 'must-revalidate']);
    }

    /**
     * Get the download File.
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
     * @param string $data The message body.
     * @return Message The Message.
     */
    public function setBody(string $data): static
    {
        return $this;
    }

}

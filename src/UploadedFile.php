<?php
declare(strict_types=1);

namespace Fyre\Server;

use Fyre\FileSystem\Exceptions\FileSystemException;
use Fyre\FileSystem\File;
use Fyre\FileSystem\Folder;
use Fyre\Server\Exceptions\ServerException;
use Fyre\Utility\Path;

use function is_uploaded_file;
use function move_uploaded_file;

use const UPLOAD_ERR_OK;

/**
 * UploadedFile
 */
class UploadedFile extends File
{
    protected int $error;

    protected bool $hasMoved = false;

    protected string|null $mimeType;

    protected string $originalName;

    /**
     * New UploadedFile constructor.
     *
     * @param array $data The uploaded file data.
     */
    public function __construct(array $data)
    {
        parent::__construct($data['tmp_name']);

        $this->originalName = $data['name'] ?? '';
        $this->mimeType = $data['type'] ?? null;
        $this->error = $data['error'] ?? UPLOAD_ERR_OK;
    }

    /**
     * Get the client extension.
     *
     * @return string The client extension.
     */
    public function clientExtension(): string
    {
        return Path::extension($this->originalName);
    }

    /**
     * Get the client MIME type.
     *
     * @return string The client MIME type.
     */
    public function clientMimeType(): string|null
    {
        return $this->mimeType;
    }

    /**
     * Get the client filename.
     *
     * @return string The client filename.
     */
    public function clientName(): string
    {
        return $this->originalName;
    }

    /**
     * Get the uploaded error code.
     *
     * @return int The uploaded error code.
     */
    public function error(): int
    {
        return $this->error;
    }

    /**
     * Determine if the uploaded file has been moved.
     *
     * @return bool TRUE if the uploaded file has been moved, otherwise FALSE.
     */
    public function hasMoved(): bool
    {
        return $this->hasMoved;
    }

    /**
     * Determine if the uploaded file is valid.
     *
     * @return bool TRUE if the uploaded file is valid, otherwise FALSE.
     */
    public function isValid(): bool
    {
        return is_uploaded_file($this->path) && $this->error === UPLOAD_ERR_OK;
    }

    /**
     * Move the uploaded file.
     *
     * @param string $destination The destination.
     * @param string|null $name The new filename.
     * @return File The new File.
     *
     * @throws ServerException if the upload is not valid.
     * @throws FileSystemExcetion if the file could not be moved.
     */
    public function moveTo(string $destination, string|null $name = null): File
    {
        if ($this->hasMoved) {
            throw ServerException::forUploadAlreadyMoved($this->originalName);
        }

        if (!$this->isValid()) {
            throw ServerException::forUploadInvalid($this->originalName);
        }

        $folder = new Folder($destination, true);

        $name ??= $this->clientName();
        $path = Path::join($folder->path(), $name);

        $this->hasMoved = move_uploaded_file($this->path, $path);

        if ($this->hasMoved === false) {
            throw FileSystemException::forLastError();
        }

        return new File($path);
    }
}

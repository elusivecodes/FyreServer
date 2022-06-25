<?php
declare(strict_types=1);

namespace Fyre\Server\Exceptions;

use
    RuntimeException;

/**
 * ServerException
 */
class ServerException extends RuntimeException
{

    public static function forInvalidNegotiationType(string $type): static
    {
        return new static('Invalid negotation type: '.$type);
    }

    public static function forMissingFile(string $path): static
    {
        return new static('Download file does not exist: '.$path);
    }

    public static function forUploadAlreadyMoved(string $filename): static
    {
        return new static('Upload already moved: '.$filename);
    }

    public static function forUploadInvalid(string $filename): static
    {
        return new static('Upload is not valid: '.$filename);
    }

}

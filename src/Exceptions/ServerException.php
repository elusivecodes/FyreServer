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

    public static function forInvalidNegotiationType(string $type): self
    {
        return new static('Invalid negotation type: '.$type);
    }

    public static function forUploadAlreadyMoved(string $filename): self
    {
        return new static('Upload already moved: '.$filename);
    }

    public static function forUploadInvalid(string $filename): self
    {
        return new static('Upload is not valid: '.$filename);
    }

}

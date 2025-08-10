<?php 

namespace App\Message;

use Symfony\Component\Uid\Ulid;

final class TranscodeMessage
{
    public function __construct(public readonly Ulid|string $movieId) {}
}

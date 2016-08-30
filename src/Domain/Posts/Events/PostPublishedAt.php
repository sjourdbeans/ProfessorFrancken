<?php

namespace Francken\Domain\Posts\Events;

use Carbon\Carbon;

use Francken\Domain\Posts\PostId;
use Francken\Domain\Base\DomainEvent;
use Broadway\Serializer\SerializableInterface;
use BroadwaySerialization\Serialization\Serializable;

final class PostPublishedAt implements SerializableInterface
{
    use Serializable;

    private $postId;
    private $date;

    public function __construct(PostId $postId, Carbon $date)
    {
        $this->postId = $postId;
        $this->date = $date;
    }

    public function postId() : PostId
    {
        return $this->postId;
    }

    public function date() : Carbon
    {
        return $this->date;
    }

    protected static function deserializationCallbacks()
    {
        return [
            'postId' => [PostId::class, 'deserialize']
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    private mixed $id;

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($post) {
            $post->indexToElasticsearch();
        });

        static::deleted(function ($post) {
            $post->removeFromElasticsearch();
        });
    }

    public function indexToElasticsearch(): void
    {
        $client = app('Elasticsearch');

        $client->index([
            'index' => 'posts',
            'id'    => $this->id,
            'body'  => $this->toArray()
        ]);
    }

    public function removeFromElasticsearch(): void
    {
        $client = app('Elasticsearch');

        $client->delete([
            'index' => 'posts',
            'id'    => $this->id,
        ]);
    }
}

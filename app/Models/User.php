<?php

namespace App\Models;

use App\Services\ElasticsearchService;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected ElasticsearchService $elasticsearch;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->elasticsearch = new ElasticsearchService();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($user) {
            $user->indexToElasticsearch();
        });

        static::deleting(function ($user) {
            $user->removeFromElasticsearch();
        });
    }

    /**
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indexToElasticsearch(): void
    {
        // Get the attributes that should be indexed
        $attributes = $this->only(['name', 'email', 'role']);

        // Convert the created_at and updated_at fields to ISO 8601 format
        if (isset($this->created_at))
        {
            $attributes['created_at'] = $this->created_at
                ->toIso8601String();
        }
        if (isset($this->updated_at))
        {
            $attributes['updated_at'] = $this->updated_at
                ->toIso8601String();
        }

        $this->elasticsearch->getClient()->index([
            'index' => 'users',
            'id'    => $this->getKey(),
            'body'  => $attributes
        ]);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function removeFromElasticsearch(): void
    {
        $this->elasticsearch->getClient()
            ->delete([
            'index' => 'users',
            'id'    => $this->getKey(),
        ]);
    }
}

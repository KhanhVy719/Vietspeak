<?php

namespace App\Traits;

use Godruoyi\Snowflake\Snowflake;
use Godruoyi\Snowflake\LaravelSequenceResolver;

trait HasSnowflakeId
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootHasSnowflakeId()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                // Initialize Snowflake with a WorkerID (can be configured based on server/process ID)
                // For simple setup, we use random or fixed. For distributed, this needs to be unique per node.
                $snowflake = new Snowflake();
                
                // Optional: Use Laravel Cache to manage sequence if running multiple processes
                // $snowflake->setSequenceResolver(new LaravelSequenceResolver($model->getCache()));

                $model->{$model->getKeyName()} = $snowflake->id();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string'; // Snowflake ID is a large integer, but PHP handles huge ints better as strings or if 64-bit is guaranteed.
                         // However, for API compatibility, string is safest. 
                         // If database column is BIGINT, Laravel casts it to int automatically if we don't specify string here?
                         // Actually, for Snowflake (bigint), we should treat it as integer if PHP 64bit.
                         // But JavaScript loses precision on large integers > 2^53. 
                         // So it is HIGHLY RECOMMENDED to cast to string for JSON responses.
                         // Let's return 'string' to force Laravel to cast it to string when serializing to JSON.
    }
}

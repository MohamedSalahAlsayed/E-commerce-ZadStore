<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait SyncToMysql
{
    /** Cache MySQL availability for the duration of one request to avoid repeated timeouts. */
    protected static ?bool $mysqlAvailable = null;

    protected static function checkMysqlAvailable(): bool
    {
        if (static::$mysqlAvailable !== null) {
            return static::$mysqlAvailable;
        }
        try {
            DB::connection('mysql')->getPdo();
            static::$mysqlAvailable = true;
        } catch (\Exception $e) {
            static::$mysqlAvailable = false;
        }
        return static::$mysqlAvailable;
    }

    /**
     * Boot the trait and register Eloquent events.
     */
    public static function bootSyncToMysql()
    {
        static::saved(function ($model) {
            if (!static::checkMysqlAvailable()) return;
            try {
                $table = $model->getTable();
                $data  = $model->getAttributes();
                DB::connection('mysql')->table($table)->updateOrInsert(
                    [$model->getKeyName() => $model->getKey()],
                    $data
                );
            } catch (\Exception $e) {
                static::$mysqlAvailable = false;
                Log::error("Failed to sync saved event to MySQL for table {$model->getTable()}: " . $e->getMessage());
            }
        });

        static::deleted(function ($model) {
            if (!static::checkMysqlAvailable()) return;
            try {
                $table = $model->getTable();
                DB::connection('mysql')->table($table)
                    ->where($model->getKeyName(), $model->getKey())
                    ->delete();
            } catch (\Exception $e) {
                static::$mysqlAvailable = false;
                Log::error("Failed to sync deleted event to MySQL for table {$model->getTable()}: " . $e->getMessage());
            }
        });
    }
}

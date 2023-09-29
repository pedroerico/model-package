<?php

namespace Model;

use InvalidArgumentException;

class Model
{
    protected static string $table;

    protected static ?string $path;

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    protected function fill(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private static function loadData(string $filename): array
    {
        $dataPath = defined('DATA_PATH');
        $filePath = ($dataPath ? DATA_PATH : static::$path) . $filename . '.json';
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("O arquivo '$filePath' não existe.");
        }
        $data = file_get_contents($filePath);
        return json_decode($data, true);
    }

    public static function setPath(string $path): void
    {
        static::$path = $path;
    }

    public static function setTable(string $table): void
    {
        static::$table = $table;
    }

    public static function all(): array
    {
        $records = self::loadData(static::$table);
        return array_map(fn($record) => new static($record), $records);
    }

    public static function find(int $id): ?self
    {
        $records = self::loadData(static::$table);
        $filteredRecords = array_filter($records, fn($record) => $record['id'] == $id);
        $record = array_shift($filteredRecords);

        return match ($record) {
            null => null,
            default => new static($record),
        };
    }

    protected function hasMany(string $relatedModel, string $foreignKey, string $primaryKey = 'id'): array
    {
        if ($this->{$primaryKey} == null) {
            return [];
        }
        $instance = new $relatedModel();
        $records = self::loadData($instance::$table);
        $filteredRecords = array_filter($records, fn($record) => $record[$foreignKey] == $this->{$primaryKey});
        return array_map(fn($record) => new static($record), $filteredRecords);
    }

    protected function belongsTo(string $relatedModel, string $foreignKey, string $primaryKey = 'id'): ?self
    {
        if ($this->{$primaryKey} == null) {
            return null;
        }
        $instance = new $relatedModel();
        $records = self::loadData($instance::$table);
        foreach ($records as $record) {
            if ($record[$primaryKey] === $this->{$foreignKey}) {
                return new static($record);
            }
        }
        return null;
    }
}

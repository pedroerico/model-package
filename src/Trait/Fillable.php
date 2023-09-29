<?php

namespace Trait;

use Attributes\Column;
use ReflectionClass;

trait Fillable
{
    public function fill(array $data): void
    {
        $propertyToColumnMap = $this->getPropertyToColumnMap();

        foreach ($data as $columnName => $value) {
            if (isset($propertyToColumnMap[$columnName])) {
                $property = $propertyToColumnMap[$columnName];
                $this->{$property} = $value;
            }
        }
    }

    protected function getPropertyToColumnMap(): array
    {
        $reflection = new ReflectionClass($this);
        $propertyToColumnMap = [];

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(Column::class);

            if (!empty($attributes)) {
                $column = $attributes[0]->newInstance()->name;
                $propertyToColumnMap[$column] = $property->getName();
            }
        }

        return $propertyToColumnMap;
    }
}

<?php

namespace Attributes;

#[\Attribute]
class Column
{
    public function __construct(public string $name)
    {
    }
}

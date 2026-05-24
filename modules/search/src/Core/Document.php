<?php

namespace SearchPHP\Core;

class Document
{
    private $id;
    private $fields;

    public function __construct(string $id, array $fields = [])
    {
        $this->id = $id;
        $this->fields = $fields;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $name)
    {
        return $this->fields[$name] ?? null;
    }
}

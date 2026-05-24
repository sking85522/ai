<?php
namespace Core\Commands;

interface CommandInterface {
    public function canProcess(string $task): bool;
    public function process(string $task): string;
}

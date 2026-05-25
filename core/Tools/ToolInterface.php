<?php
namespace Core\Tools;

interface ToolInterface {
    public function execute(array $input = []): array;
}

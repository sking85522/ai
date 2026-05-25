<?php
namespace Core\Tools;

use Core\Tools\FileSystem\FileEditor;

class ReadFileTool implements ToolInterface {
    private FileEditor $files;

    public function __construct() {
        $this->files = new FileEditor();
    }

    public function execute(array $input = []): array {
        return $this->files->readFile((string)($input['path'] ?? ''), (int)($input['max_bytes'] ?? 4000));
    }
}

<?php
namespace Core\Tools;

use Core\Tools\FileSystem\FileEditor;

class WriteFileTool implements ToolInterface {
    private FileEditor $files;

    public function __construct() {
        $this->files = new FileEditor();
    }

    public function execute(array $input = []): array {
        $path = (string)($input['path'] ?? '');
        $content = (string)($input['content'] ?? '');
        $ok = $this->files->writeFile($path, $content);

        return $ok
            ? ['status' => 'success', 'path' => $path, 'bytes' => strlen($content)]
            : ['status' => 'error', 'message' => 'Unable to write file.'];
    }
}

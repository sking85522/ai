<?php
namespace Core\Tools\Documentation;

use Core\Tools\FileSystem\FileEditor;

class AutoScribe {
    private FileEditor $files;

    public function __construct() {
        $this->files = new FileEditor();
    }

    public function generateReadme(): string {
        $tree = implode("\n", array_slice($this->files->listFiles('.', 2), 0, 120));
        $content = "# Hritik AI Generated Overview\n\n" .
                   "This file was generated from the current local project structure.\n\n" .
                   "## Structure\n\n```text\n{$tree}\n```\n";
        $this->files->writeFile('GENERATED_README.md', $content);

        return '[DOCS] Generated GENERATED_README.md with a project overview.';
    }

    public function documentFolder(string $folder): string {
        $items = $this->files->listFiles(trim($folder), 2);
        if (empty($items)) {
            return '[DOCS] Folder not found or empty.';
        }

        return "[DOCS] Folder: {$folder}\n" . implode("\n", array_slice($items, 0, 80));
    }
}

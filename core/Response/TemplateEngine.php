<?php
class TemplateEngine
{
    public function render($template, $vars = [])
    {
        $output = (string) $template;
        foreach ($vars as $key => $value) {
            $output = str_replace('{{' . $key . '}}', (string) $value, $output);
        }
        return $output;
    }
}

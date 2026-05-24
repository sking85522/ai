<?php
namespace Core\Response;

class TemplateEngine {
    /**
     * Replaces placeholders like {NAME} or {TIME} with actual values.
     */
    public function render(string $template, array $data = []): string {
        $placeholders = [
            '{TIME}' => date('h:i A'),
            '{DATE}' => date('d-m-Y'),
            '{NAME}' => 'Hritik AI',
            '{CREATOR}' => 'Sachin (Hritik Softwares)'
        ];

        $allData = array_merge($placeholders, $data);
        
        return strtr($template, $allData);
    }
}

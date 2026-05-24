<?php
namespace Core\Tools\Search;

class WikiSearch {
    public function search(string $query): ?string {
        $query = trim($query);
        if ($query === '' || !function_exists('curl_init')) {
            return null;
        }

        $url = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode(ucwords($query));
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_USERAGENT => 'HritikAI/1.0'
        ]);
        $response = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        return !empty($data['extract']) ? trim($data['extract']) : null;
    }
}

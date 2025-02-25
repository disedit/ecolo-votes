<?php

namespace App\Libraries;

use Curl\Curl;

class Jotform {
    protected $key;
    protected $domain;
    protected $curl;
    
    /**
     * Create a Jotform instance
     */
    public function __construct() {
        $this->key = config('services.jotform.key');
        $this->domain = config('services.jotform.domain');
        $this->curl = new Curl();
    }

    private function get($path) {
        return $this->curl->get('https://' . $this->domain . '/' . $path, [
            'apiKey' => $this->key,
            'limit' => 1000
        ]);
    }
    
    public function submissions(String $formId)
    {
        $response = $this->get('form/' . $formId . '/submissions');
        return $response;
    }
}
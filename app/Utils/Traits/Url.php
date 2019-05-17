<?php

namespace App\Utils\Traits;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Url
{
    /**
     * @param $url
     * @param $path
     * @return string
     */
    public function storeFileFromUrl($url, $path = '/')
    {
        try {
            if (empty($this->client)) {
                $this->client = new Client();
            }
            $filename = Str::random(40);
            $contents = $this->client->get($url)->getBody()->getContents();
            Storage::disk('public')->put($path . $filename, $contents);
            return $path . $filename;
        } catch (RequestException $exception) {
            return "";
        }
    }
}

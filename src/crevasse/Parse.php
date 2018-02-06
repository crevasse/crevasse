<?php

namespace Crevasse;

use Crevasse\Kernel;
use Crevasse\CurlX;
use Crevasse\ParseException;

class Parse extends Kernel
{
    public $config_type = null;
    public $config_data = [];
    public $curl_response = [];
    public $output_info = null;

    public function __toString()
    {
        return (string) $this->output_info;
    }

    public function __construct($config_data)
    {
        if (!isset($config_data)) {
            new ParseException([
                'class'=> __CLASS__,
                'function'=>__FUNCTION__,
                'message'=> 'config_data is null!',
                'status'=>200
            ]);
        }
        switch ($config_data) {
            case preg_match('(http://|https://)',$config_data) == true:
                $this->config_type = 'url_config';
                $this->getUrlResponse($config_data);
                $this->parseContent($this->curl_response);
                $this->replaceDefaultPolicy();
                $this->setPatchConvert();
                $this->setImportType();
                $this->getOutputResult();
                break;
            case is_object(json_decode(base64_decode($config_data))):
                $this->config_type = 'raw_config';
                $this->config_data = json_decode(base64_decode($config_data),true);
                $this->parseContent($this->config_data);
                $this->replaceDefaultPolicy();
                $this->setPatchConvert();
                $this->setImportType();
                $this->getOutputResult();
                break;
            default:
        }
    }

    public function getUrlResponse(string $url = null)
    {
        if (preg_match('(http://|https://)',$url) == true) {
            $this->curl_response = null;
            $headers = get_headers($url);
            switch ($headers) {
                case $headers['Content-Length'] < '10240':
                    $curl = new CurlX();
                    $curl->get($url);
                    !$curl->error ?
                        $this->curl_response = json_decode($curl->setDisableTransfer($curl->response),true)
                        : $this->curl_response = [];
                    return $this->curl_response;
                    break;
                case $headers['Content-Length'] > '10240':
                    new ParseException([
                        'class'=> __CLASS__,
                        'function'=>__FUNCTION__,
                        'message'=> 'url file size exceeded 10240 bytes.',
                        'status'=>200
                    ]);
                    break;
            }
        }
        return false;
    }

    public function parseContent(array $config_data)
    {
        if (count($config_data) > 50) {
            new ParseException([
                'class'=>__CLASS__,
                'method'=>__METHOD__,
                'message'=>'The amount of configurations exceeds the maximum limit.',
                'status'=>500
            ]);
        }
        foreach ($config_data as $item => $value) {
            switch ($item) {
                case 'convert_info':
                    $this->getUrlResponse($value) ?
                        $this->{$item} = $this->getUrlResponse($value)
                        : $this->{$item} = [];
                    break;
                case 'convert_patch':
                    $this->getUrlResponse($value) ?
                        $this->{$item} = $this->getUrlResponse($value)
                        : $this->{$item} = [];
                    break;
                case $this->{$item} === []:
                    $this->{$item} = $value;
                    break;
                case is_string($this->{$item}) === true:
                    $this->{$item} = $value;
                    break;
                default:
            }
        }
    }
}
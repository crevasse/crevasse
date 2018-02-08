<?php

namespace Crevasse;

use Crevasse\Kernel;
use Crevasse\CurlX;
use Crevasse\ParseException;

class Parse extends Kernel
{
    public $info_type = null;
    public $user_info = [];
    public $curl_response = [];
    public $output_info = null;

    public function __toString()
    {
        return (string) $this->output_info;
    }

    public function __construct($user_info)
    {
        if (!isset($config_data)) {
            new ParseException([
                'class'=> __CLASS__,
                'function'=>__FUNCTION__,
                'message'=> 'user_info is null!',
                'status'=>200
            ]);
        }
        switch ($user_info) {
            case preg_match('(http://|https://)',$user_info) == true:
                $this->info_type = 'url_info';
                $this->getUrlResponse($user_info);
                $this->parseContent($this->curl_response);
                $this->replaceRulePolicy();
                $this->setPatchConvert();
                $this->setEnableInfo();
                $this->getOutputResult();
                break;
            case is_object(json_decode(base64_decode($user_info))):
                $this->info_type = 'raw_info';
                $this->user_info = json_decode(base64_decode($user_info),true);
                $this->parseContent($this->user_info);
                $this->replaceRulePolicy();
                $this->setPatchConvert();
                $this->setEnableInfo();
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

    public function parseContent(array $user_info)
    {
        if (count($user_info) > 50) {
            new ParseException([
                'class'=>__CLASS__,
                'method'=>__METHOD__,
                'message'=>'The amount of configurations exceeds the maximum limit.',
                'status'=>500
            ]);
        }
        foreach ($user_info as $item => $value) {
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
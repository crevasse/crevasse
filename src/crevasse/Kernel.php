<?php

namespace Crevasse;

use Crevasse\KernelException;

class Kernel
{
    public $hash_key = [];
    public $hash_value = [];
    public $server_list = [];
    public $group_list = [];
    public $server_info = null;
    public $group_info = null;
    public $output_info = null;
    public $proxy_info = [];
    public $rules_policy = [];
    public $enable_info = [];
    public $managed_info = [];
    public $convert_info = [];
    public $convert_patch = [];
    public $output_format = '';
    public $module_url = '';

    public function getOutputResult()
    {
        switch($this->output_format) {
            case 'surge':
                $this->setRuleManaged();
                $this->setRuleGeneral();
                $this->setRuleSkipProxy();
                $this->setRuleBypassTun();
                $this->setRuleDnsServer();
                $this->setRuleReplica();
                $this->parseProxyServer();
                $this->setRuleProxy();
                $this->parseProxyGroup();
                $this->setRuleProxyGroup();
                $this->setRuleContent();
                $this->setRuleGeoIp();
                $this->setRuleFinal();
                $this->setRuleHost();
                $this->setRuleHeaderRewrite();
                $this->setRuleUrlRewrite();
                $this->setRuleSSIDSetting();
                $this->setRuleMITM();
                break;
            default:
                new KernelException([
                    'class'=> __CLASS__,
                    'function'=>__FUNCTION__,
                    'message'=> 'output_format -> is null.',
                    'status'=>200
                ]);
        }
    }

    public function parseProxyServer()
    {
        if (count($this->proxy_info['server_list']) < 1) {
            new KernelException([
                'class'=> __CLASS__,
                'function'=>__FUNCTION__,
                'message'=> 'proxy_info -> server_list is null.',
                'status'=>200
            ]);
        }
        $this->server_list = $this->proxy_info['server_list'];
        for ($i=0; $i<count($this->server_list); $i++) {
            switch ($this->server_list[$i]) {
                case $this->server_list[$i]['type'] === 'custom':
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['name'], ' = ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['type'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['server'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['port'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['method'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['password'], ', ');
                    $this->convertToString('module', 'server_info', $this->module_url, ', ');
                    $this->convertToString('server_option', 'server_info', $this->server_list[$i]['option'], ', ');
                    $this->server_info = rtrim($this->server_info,', ');
                    $this->server_info.="\r\n";
                    break;
                case $this->server_list[$i]['type'] === 'https' || 'socks5-tls':
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['name'], ' = ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['type'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['server'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['port'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['username'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['password'], ', ');
                    $this->convertToString('server_option', 'server_info', $this->server_list[$i]['option'], ', ');
                    $this->server_info = rtrim($this->server_info,', ');
                    $this->server_info.="\r\n";
                    break;
                case $this->server_list[$i]['type'] === 'http' || 'socks5':
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['name'], ' = ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['type'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['server'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['port'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['username'], ', ');
                    $this->convertToString('server', 'server_info', $this->server_list[$i]['password'], ', ');
                    $this->server_info = rtrim($this->server_info,', ');
                    $this->server_info.="\r\n";
                    break;
                default:
            }
        }
    }

    public function parseProxyGroup()
    {
        if (count($this->proxy_info['group_list']) < 1) {
            new KernelException([
                'class'=> __CLASS__,
                'function'=>__FUNCTION__,
                'message'=> 'proxy_info -> group_list is null.',
                'status'=>200
            ]);
        }
        $this->group_list = $this->proxy_info['group_list'];
        for ($i=0; $i<count($this->group_list); $i++) {
            switch ($this->group_list[$i]) {
                case $this->group_list[$i]['type'] === 'select':
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['name'], ' = ');
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['type'], ', ');
                    $this->convertToString('list', 'group_info', $this->group_list[$i]['list'], ', ');
                    $this->group_info = rtrim($this->group_info,', ');
                    $this->group_info.="\r\n";
                    break;
                case $this->group_list[$i]['type'] === 'ssid':
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['name'], ' = ');
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['type'], ', ');
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['default'], ', ','default = ');
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['cellular'], ', ','cellular = ');
                    $this->convertToString('ssid', 'group_info', $this->group_list[$i]['list'], ', ');
                    $this->group_info = rtrim($this->group_info,', ');
                    $this->group_info.="\r\n";
                    break;
                case $this->group_list[$i]['type'] === 'url-test' || 'fallback':
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['name'], ' = ');
                    $this->convertToString('group', 'group_info', $this->group_list[$i]['type'], ', ');
                    $this->convertToString('list', 'group_info', $this->group_list[$i]['list'], ', ');
                    $this->convertToString('group_option', 'group_info', $this->group_list[$i]['option'], ', ');
                    $this->group_info = rtrim($this->group_info,', ');
                    $this->group_info.="\r\n";
                    break;
                default:
            }
        }
    }

    public function convertToString($type=null, $object=null, $content=null, $afterCombine=null, $beforeCombine=null)
    {
        if (isset($type) && isset($object) && isset($content)) {
            switch ($type) {
                case $type == 'server' || $type == 'group' || $type == 'rules' || $type == 'module':
                    $this->{$object}.= $beforeCombine.$content.$afterCombine;
                    break;
                case $type == 'server_option' || $type == 'group_option':
                    foreach ($content as $key => $value) {
                        $this->{$object}.= $beforeCombine.$key.' = '.$value.$afterCombine;
                    }
                    $this->{$object} = rtrim($this->{$object},$afterCombine);
                    break;
                case $type == 'list':
                    foreach ($content as $key => $value) {
                        $this->{$object}.= $beforeCombine.$value.$afterCombine;
                    }
                    break;
                case $type == 'ssid':
                    foreach ($content as $key => $value) {
                        $this->{$object}.= $beforeCombine."\"{$key}\" = ".$value.$afterCombine;
                    }
                    break;
                case $type == 'mitm':
                    foreach ($content as $key => $value) {
                        $this->{$object}.= $key.' = '.$value.$afterCombine;
                    }
                    break;
                default:
            }
        }
    }

    public function splitHashValue($hash_data)
    {
        if(is_array($hash_data)) {
            $this->hash_key = array_keys($hash_data);
            $this->hash_value = array_values($hash_data);
        }
    }

    public function replaceRulePolicy()
    {
        if(!isset($this->convert_info['rules']) || !isset($this->rules_policy)) {
            new KernelException([
                'class'=> __CLASS__,
                'function'=>__FUNCTION__,
                'message'=> 'convert_info rules is null.',
                'status'=>500
            ]);
        }
        $this->splitHashValue($this->convert_info['rules']);
        for ($i=0; $i<count($this->hash_value); $i++) {
            for ($j=0; $j<count($this->rules_policy); $j++) {
                foreach ($this->rules_policy as $item => $val) {
                    if ($item === $this->hash_value[$i]['policy']) {
                        $this->hash_value[$i]['policy'] = $val;
                        $this->convert_info['rules'][$this->hash_key[$i]] = $this->hash_value[$i];
                    }
                }
            }
        }
        return $this->convert_info;
    }

    public function setPatchConvert()
    {
        isset($this->convert_info) ?
            $convert_info = $this->convert_info : $convert_info = [];
        isset($this->convert_patch) ?
            $convert_patch = $this->convert_patch : $convert_patch = [];
        $this->convert_info = array_replace_recursive($convert_info,$convert_patch);
        isset($this->convert_info['rules']) ?
            asort($this->convert_info['rules']) : false;
        return $this->convert_info;
    }

    public function setEnableInfo()
    {
        $this->splitHashValue($this->convert_info);
        for ($i=0; $i<count($this->hash_key); $i++) {
            if (!in_array($this->hash_key[$i],$this->enable_info['label_type'])) {
                $this->convert_info[$this->hash_key[$i]] = null;
            }
        }
        $convert_info = $this->convert_info['rules'];
        $this->splitHashValue($convert_info);
        for ($i=0; $i<count($this->hash_value); $i++) {
            if (!in_array($this->hash_value[$i]['type'],$this->enable_info['rules_type'])) {
                $this->hash_value[$i] = null;
                $this->convert_info['rules'][$this->hash_key[$i]] = $this->hash_value[$i];
            }
        }
        return $this->convert_info;
    }

    public function hashArrayToString(string $after_combine = null, string $before_combine = null)
    {
        if (is_array($this->hash_key)) {
            $result = null;
            for ($i=0; $i<count($this->hash_key); $i++) {
                if(isset($this->hash_value[$i])) {
                    $result.= $before_combine.$this->hash_value[$i].$after_combine;
                }
            }
            return rtrim($result, $after_combine);
        }
        return false;
    }

    public function setRuleLabel($labelName)
    {
        if (isset($this->convert_info['label'][$labelName])) {
            $this->output_info.= $this->convert_info['label'][$labelName]."\r\n";
        }
    }

    public function setRuleGeneral()
    {
        if (isset($this->convert_info['general'])) {
            $this->output_info.= "\r\n";
            $this->setRuleLabel('general');
            $this->splitHashValue($this->convert_info['general']);
            for ($i=0; $i<count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['name'], ' = ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['value'], "\r\n");
            }
        }
    }

    public function setRuleContent()
    {
        if (isset($this->convert_info['rules'])) {
            $this->setRuleLabel('rule');
            $this->splitHashValue($this->convert_info['rules']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['type'], ',');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['value'], ',');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['policy']);
                $this->hash_value[$i]['option'] ?
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['option'], "\r\n", ',')
                    : $this->output_info.= "\r\n";
            }
        }
    }

    public function setRuleGeoIp()
    {
        if (isset($this->convert_info['geoip'])) {
            $this->splitHashValue($this->convert_info['geoip']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['type'], ',');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['region'], ',');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['policy']);
                $this->hash_value[$i]['option'] ?
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['option'], "\r\n", ',')
                    : $this->output_info.= "\r\n";
            }
        }
    }

    public function setRuleFinal()
    {
        if (isset($this->convert_info['final'])) {
            $this->splitHashValue($this->convert_info['final']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['type'], ',');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['policy']);
                $this->hash_value[$i]['option'] ?
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['option'], "\r\n", ',')
                    : $this->output_info.= "\r\n";
            }
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleHost()
    {
        if (isset($this->convert_info['host'])) {
            $this->setRuleLabel('host');
            $this->splitHashValue($this->convert_info['host']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['host'], ' = ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['redirect'], "\r\n");
            }
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleUrlRewrite()
    {
        if (isset($this->convert_info['url_rewrite'])) {
            $this->setRuleLabel('url_rewrite');
            $this->splitHashValue($this->convert_info['url_rewrite']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['pattern'], ' ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['replace'], ' ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['policy'], "\r\n");
            }
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleHeaderRewrite()
    {
        if (isset($this->convert_info['header_rewrite'])) {
            $this->setRuleLabel('header_rewrite');
            $this->splitHashValue($this->convert_info['header_rewrite']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['value'], ' ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['action'], ' ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['header_name'], ' ');
                $this->hash_value[$i]['header_value'] ?
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['header_value'], "\r\n")
                    : $this->output_info.= "\r\n";
            }
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleMITM()
    {
        if (isset($this->convert_info['mitm'])) {
            $this->setRuleLabel('mitm');
            isset($this->convert_info['mitm']['enable']) ?
                $this->output_info.= 'enable = '.
                    $this->convert_info['mitm']['enable']."\r\n" :false;
            $this->splitHashValue($this->convert_info['mitm']['hostname']);
            $this->output_info.= 'hostname = '.$this->hashArrayToString(', ')."\r\n";
            isset($this->convert_info['mitm']['ca-passphrase']) ?
                $this->output_info.= 'ca-passphrase = '.
                    $this->convert_info['mitm']['ca-passphrase']."\r\n" :false;
            isset($this->convert_info['mitm']['ca-p12']) ?
                $this->output_info.= 'ca-p12 = '.
                    $this->convert_info['mitm']['ca-p12']."\r\n" :false;
            $this->splitHashValue($this->convert_info['mitm']['option']);
            for ($i=0; $i<count($this->hash_key); $i++) {
                if(isset($this->hash_value[$i]['name']) || isset($this->hash_value[$i]['value'])) {
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['name'], ' = ');
                    $this->convertToString('rules', 'output_info', $this->hash_value[$i]['value'], "\r\n");
                }
            }
        }
    }

    public function setRuleBypassTun()
    {
        if (isset($this->convert_info['bypass-tun'])) {
            $this->splitHashValue($this->convert_info['bypass-tun']['list']);
            $this->output_info.= 'bypass-tun = '.$this->hashArrayToString(', ')."\r\n";
        }
    }

    public function setRuleDnsServer()
    {
        if (isset($this->convert_info['dns-server'])) {
            $this->splitHashValue($this->convert_info['dns-server']['list']);
            $this->output_info.= 'dns-server = '.$this->hashArrayToString(', ')."\r\n";
        }
    }

    public function setRuleSkipProxy()
    {
        if (isset($this->convert_info['skip-proxy'])) {
            $this->splitHashValue($this->convert_info['skip-proxy']['list']);
            $this->output_info.= 'skip-proxy = '.$this->hashArrayToString(', ')."\r\n";
        }
    }

    public function setRuleProxy()
    {
        if (isset($this->server_info)) {
            $this->output_info.= "\r\n";
            $this->setRuleLabel('proxy');
            $this->output_info.=$this->server_info."\r\n";
        }
    }

    public function setRuleReplica()
    {
        if (isset($this->convert_info['replica'])) {
            $this->output_info.= "\r\n";
            $this->setRuleLabel('replica');
            $this->splitHashValue($this->convert_info['replica']['option']);
            for ($i=0; $i<count($this->hash_key); $i++) {
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['name'], ' = ');
                $this->convertToString('rules', 'output_info', $this->hash_value[$i]['value'], "\r\n");
            }
            $this->splitHashValue($this->convert_info['replica']['keyword-filter']);
            $this->output_info.= 'keyword-filter = '.$this->hashArrayToString(',')."\r\n";
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleProxyGroup()
    {
        if (isset($this->group_info)) {
            $this->setRuleLabel('proxy_group');
            $this->output_info.= $this->group_info."\r\n";
        }
    }

    public function setRuleSSIDSetting()
    {
        if (isset($this->convert_info['ssid_setting'])) {
            $this->setRuleLabel('ssid_setting');
            $this->splitHashValue($this->convert_info['ssid_setting']);
            for ($i=0; $i < count($this->hash_key); $i++) {
                $this->output_info.= $this->hash_value[$i]['ssid'].
                    ' suspend = '. $this->hash_value[$i]['suspend']."\r\n";
            }
            $this->output_info.= "\r\n";
        }
    }

    public function setRuleManaged()
    {
        if (isset($this->managed_info)) {
            $this->managed_info['interval'] ?
                $interval = $this->managed_info['interval'] : $interval = 86400;
            $this->managed_info['strict'] ?
                $strict = $this->managed_info['strict'] : $strict = "false";
            $this->output_info.= "#!MANAGED-CONFIG "."http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']} ".
                "interval={$interval} strict={$strict}"."\r\n";
        }
    }
}
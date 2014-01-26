<?php

/**
 * SolusVM XMLRPC API PHP Library
 *
 * PHP Library for easy integration of Solusvm <http://www.solusvm.com>.
 *
 * @category   PHP Libraries
 * @package    Solusvm
 * @author     Benton Snyder <introspectr3@gmail.com>
 * @copyright  2012 Noumenal Designs
 * @license    GPLv3
 * @website    <http://www.noumenaldesigns.com>
 */

class Solus
{
    private $url;
    private $key;
    private $hash;

    /**
     * Public constructor
     *
     * @access         public
     * @param          str, str, str
     * @return
     */
    function __construct($url, $key, $hash)
    {
        $this->url = $url;
        $this->key = $key;
        $this->hash = $hash;
    }

    /**
     * Executes xmlrpc api call with given parameters
     *
     * @access       private
     * @param        str, array
     * @return       str
     */
    private function execute($action, array $params = array())
    {
        // add $param data to POST variables
        foreach($params as $pKey => $pVal)
        {
            if(!is_int($pKey) && $pKey!="key" && $pKey!="hash" && $pKey!="action")
                $postfields[$pKey] = $pVal;
        }

        // inject global POST vars
        $postfields["key"] = $this->key;
        $postfields["hash"] = $this->hash;
        $postfields["action"] = $action;
        $postfields['status'] = 'true';

        // send request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $response = curl_exec($ch);

        // error handling
        if($response === false)
            throw new Exception("Curl error: " . curl_error($ch));

        // cleanup
        curl_close($ch);
        return $response;
    }

    private function _parse_response($data){
        preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $match);

        $result = array();

        foreach ($match[1] as $x => $y) {
            $result[$y] = $match[2][$x];
        }
        return $result;
    }

    public function status(){
        return $this->_parse_response($this->execute('status'));
    }

    public function checkstatus(){
        $result = $this->status();
        return $result['statusmsg'] === 'online';
    }

    public function boot(){
        $result = $this->_parse_response($this->execute('boot'));
        return $result['statusmsg'] === 'online';
    }

    public function reboot(){}

    public function shutdown(){}
}
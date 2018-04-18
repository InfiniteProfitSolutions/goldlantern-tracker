<?php
/**
 * Class used for parsing events from Gold Lantern JS tracker and saving it to queue
 *
 * @author dino.keco@gmail.com
 */
class GoldLanternTracker {
    const  VERSION = '6';

    /**
    * Cleaning unecessary proxy ip addresses if any
    */
    private function clean_ip($ip) {
        $ips = explode(",", $ip);
        $filtered_ip = end($ips);
        return trim($filtered_ip);
    }

    /**
    * Generate random string of specified length
    */
    public function rnd_str($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $characters [rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
    * Check if some parameter is value, not null, not empty not unknown
    */
    private function is_val($val) {
        return (isset($val) && trim($val) != '' && trim($val) != 'unknown');
    }

    /**
    * Get web page visitor IP regardless of proxy
    */
    private function get_ip() {
        if ($this->is_val(@$_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $this->clean_ip($_SERVER['HTTP_X_FORWARDED_FOR']);
        }else{
            return $_SERVER ['REMOTE_ADDR'];
        }
    }

    /**
    * Get web page visitor URL of HTTP referer depending on different tracking options
    */
    private function get_url(){
         if ($this->is_val(@$_GET ['u'])){
             return @$_GET ['u'];
         }
         return @$_SERVER['HTTP_REFERER'];
    }

    /**
    * Get web page visitor URL of HTTP referer depending on different tracking options
    */
    private function get_param($name, $synonym=null, $default = null){
        if ($this->is_val(@$_GET [$name])){
             return @$_GET [$name];
        }
        if ($synonym && $this->is_val(@$_GET [$synonym])){
             return @$_GET [$synonym];
        }
        return $default;
    }

    /**
    * Get web page visitor cookie
    */
    private function get_cookie(){
         if ($this->is_val(@$_COOKIE ['_gl_c_'])){
             return @$_COOKIE ['_gl_c_'];
         }
         return null;
    }

    /**
    * Get web page visitor user agent
    */
    private function get_user_agent(){
        if($this->is_val(@$_SERVER ['HTTP_USER_AGENT'])){
            return preg_replace('/[[:^print:]]/', '', @$_SERVER ['HTTP_USER_AGENT']);
        }
        return null;
    }

    /**
    * Utility method for backup tracking
    */
    private function get_server(){
        return $_SERVER ['REQUEST_SCHEME'].'://'.$_SERVER ['SERVER_NAME'].$_SERVER ['REQUEST_URI'];
    }

    /**
    * Parse as much as possible web page visitor data for tracking purposes
    */
    public function parse_event() {
        $click ['ip'] = $this->get_ip();

        $click ['timestamp'] = (int)round(microtime(true));
        $click ['day'] = date('Y-m-d', $click ['timestamp']);
        $click ['date'] = new DateTime ( $click ['timestamp'] - 28800); // mongo supports only UTC

        $click ['date_pieces'] = [
        		'date' => new DateTime ( $click ['timestamp'] - 28800),
        		'timestamp' => $click ['timestamp'],
        		'day'=> date('Y-m-d', $click ['timestamp']),
        		'day_of_week' => date('N', $click ['timestamp']),
        		'time' => date('H:i:s', $click ['timestamp']),
        		'hour_of_day' => date('G', $click ['timestamp']),
        ];

        $click ['url'] = $this->get_url();
        $click ['client'] = $this->get_param('c', 'client');
        $click ['user_agent'] = $this->get_user_agent();
        $click ['lang'] = $this->get_param('l', 'lang');
        $click ['os'] = $this->get_param('os', 'os');
        $click ['referrer'] = $this->get_param('r', 'referer');
        $click ['height'] = $this->get_param('h');
        $click ['width'] = $this->get_param('w');

        $click ['dedup'] = $this->get_param('de', 'dedup', true);
        $click ['sale_code'] = $this->get_param('sc', 'sale_code');
        $click ['product_code'] = $this->get_param('pc', 'product_code');
        $click ['products'] = json_decode($this->get_param('ps', 'products'), TRUE);
        $click ['page_type'] = $this->get_param('pt', 'page_type');
        $click ['optin'] = $this->get_param('o', 'optin');
        $click ['amount'] = $this->get_param('a', 'amount');
        $click ['labels'] = @explode(",", $_GET ['lb']);

        $click ['js_version'] = $this->get_param('jv');
        $click ['version'] = GoldLanternTracker::VERSION;
        $click ['hosted_url'] = $this->get_server();
        $click ['cookie'] = $this->get_cookie();

        $rand = substr(uniqid('', true), -5);

        if ($_SERVER['SERVER_NAME'] == 'cdn-ips.com'){
            $click ['backup'] = 1;
            $click ['bkp_id'] = md5($click['client'].$click['ip'].$click['user_agent'].$click['url'].$click['amount'].$click['optin']);
            $click ['_id'] = md5($click['client'].$click['ip'].$click['user_agent'].$click['url'].$click['amount'].$click['optin']).'-'.$click['timestamp'].'-'.$rand;
        }else{
             $click ['_id'] = md5($click['client'].$click['ip'].$click['user_agent'].$click['url'].$click['timestamp']).'-'.$rand;
             $click ['bkp_id'] = md5($click['client'].$click['ip'].$click['user_agent'].$click['url'].$click['amount'].$click['optin']);
        }

        return $click;
    }
}

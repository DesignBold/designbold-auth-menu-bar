<?php 
require(dirname(__FILE__) . '/../../../wp-config.php');

class DesignboldSampleSdk {

    private $app_key;
    private $app_secret;
    private $scope;
    private $base_url;
    private $base_api_url;
    private $base_account_url;
    private $sdk_version;
    private $urls;
    private $internal_debug;
    public $access_token;
    public $refresh_token;
    public $expire_in;
    private $authorization_code;

    /**
     * DesignboldSampleSdk constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $options_app_key = get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "";
        $options_app_secret = get_option('dbmenu_option_app_secret') != '' ? get_option('dbmenu_option_app_secret') : "";

        $this->app_key = $options_app_key;
        $this->app_secret = $options_app_secret;
        $this->redirect_url = DBMN_URL . '/designbold.php';
        $this->scope = isset($params['scope']) ? $params['scope']  : "";
        $this->sdk_version = isset($params['sdk_version']) ? $params['sdk_version'] : "v3";
        $this->internal_debug = isset($params['internal_debug']) ? $params['internal_debug'] : false;

        if ($this->internal_debug){
            $this->base_url = "https://alpha.designbold.com/". $this->sdk_version ."/";
        }
        else{
            $this->base_url = "https://designbold.com/". $this->sdk_version ."/";
        }
        if ($this->internal_debug){
            $this->base_api_url = "https://api-alpha.designbold.com/" . $this->sdk_version . "/";
        }
        else{
            $this->base_api_url = "https://api.designbold.com/" . $this->sdk_version . "/";
        }
        if ($this->internal_debug){
            $this->base_account_url = "https://accounts-alpha.designbold.com/";
        }
        else{
            $this->base_account_url = "https://accounts-alpha.designbold.com/";
        }
        $this->urls = [
            "auth" => $this->base_url . "authentication",
            "token" => $this->base_account_url . "v2/oauth/" . "token",
        ];
    }

    public function authenticate(){
        $t = time();
        $secret = $this->hash_db_secret($this->app_secret,$this->app_key,$this->redirect_url,$t);
        $query = http_build_query([
            'ak' => $this->app_key,
            'r'  => $this->redirect_url,
            'rt' => "authorization_code",
            's'  => $this->scope,
            'state' => hash('sha256',uniqid()),
        ]);

        $url = $this->getAuthenticateUrl() . '?' . $query ;

        $this->redirect($url);
    }

    public function setAuthorizationCode($code){
        $this->authorization_code = $code;
        return $this;
    }

    public function requestAccessToken()
    {
        $url = $this->get_oauth_url('token');
        $options = array(
            'method' => 'POST',
            'params' => array(
                'code'         => urlencode($this->authorization_code),
                'app_key'      => urlencode($this->app_key),
                'app_secret'   => urlencode($this->app_secret),
                'redirect_uri' => $this->redirect_url,
                'grant_type'   => 'authorization_code',
            )
        );
        $response = $this->sendCURL($url, $options);
        $data = json_decode($response['body'],true);
        if ($response['http_code'] == 200) {
            # receive token
            if (isset($data['access_token'])) {
                $this->access_token = $data['access_token'];
                $this->refresh_token = $data['refresh_token'];
                $this->expire_in = $data['expires_in'];
                return $data;
            }
            else{
                return $data;
            }
        }
        else{
            return $data;;
        }
    }

    public function redirect($url)
    {
        header('Location: ' . $url, true,  302);
        exit();
    }

    public function hash_db_secret($app_secret,$app_key,$redirect_uri,$t){
        return hash('sha256',$app_secret."#".$app_key."#".$redirect_uri."#".$t);
    }

    public function sendCURL($url, $options = [], $headers = [])
    {
        $user_agent = 'Googlebot/2.1 (+http://www.google.com/bot.html)';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if ($options['method'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (count($options['params'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['params']));
            }
        }
        elseif ($options['method'] == 'GET') {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($options['params']));
        }
        if (count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $result = array(
            'header'     => '',
            'body'       => '',
            'curl_error' => '',
            'http_code'  => '',
            'last_url'   => ''
        );

        if ($error != "") {
            $result['curl_error'] = $error;
        }
        $header_size = @curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = @curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $result;
    }

    private function get_oauth_url($scope)
    {
        if (isset($this->urls[$scope])) return $this->urls[$scope];
        return false;
    }

    public function getAuthenticateUrl(){
        return $this->base_url . "authentication";
    }
}

function connect(){
    $options_app_key = get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "";
    $options_app_secret = get_option('dbmenu_option_app_secret') != '' ? get_option('dbmenu_option_app_secret') : "";

    $designbold_sdk = new DesignboldSampleSdk([
        'app_key' => $options_app_key,
        'app_secret' => $options_app_secret,
        'redirect_url' => DBMN_URL . '/designbold.php',
        'internal_debug' => false,
        'scope' => '*.*',
    ]);
    $designbold_sdk->authenticate();
}

function callback(){
    $code = isset($_GET['code']) ? $_GET['code'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $msg = isset($_GET['msg']) ? $_GET['msg'] : 400;
    if ($status == 200){
        $options_app_key = get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "";
        $options_app_secret = get_option('dbmenu_option_app_secret') != '' ? get_option('dbmenu_option_app_secret') : "";

        $designbold_sdk = new DesignboldSampleSdk([
            'app_key' => $options_app_key,
            'app_secret' => $options_app_secret,
            'redirect_url' => DBMN_URL . '/designbold.php',
            'internal_debug' => false,
            'scope' => '*.*',
        ]);
        $designbold_sdk->setAuthorizationCode($code);
        try{
            $result = $designbold_sdk->requestAccessToken();
            if (!isset($result['access_token'])){
                echo 'No create access token';
            }
            else{
                echo "
                <script>
                try {
                    if (window.opener !== null) {
                        if (typeof window.opener.signUpComplete === 'function') {
                            window.onunload = function () {
                                window.opener.signUpComplete('".$designbold_sdk->access_token."', '".$designbold_sdk->refresh_token."');
                            };
                        }
                        else {
                            window.opener.location.reload(true);
                        }
                        window.focus();
                        window.close();
                    }
                    else {

                    }
                }
                catch (err) {

                }
                </script>";
            }
        }
        catch (Exception $exception){
            echo 'Error, somthing went wrong.';
        }
    }
    else{
        echo 'Error, access denied';
    }
    exit();
}
?>
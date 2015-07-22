<?php
namespace Common\Api;
class QQConnect{
    /**
     * ��ȡQQconnect Login ��ת���ĵ�ֵַ
     * @return array ���ذ���code state
     *
     **/
    public function login($app_id, $callback, $scope){
        $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
            .$app_id. "&redirect_uri=" . urlencode($callback)
            . "&state=" . $_SESSION['state']
            . "&scope=".urlencode($scope);
        //��ʾ����¼��ַ
        header('Location:'.$login_url);
    }
    /**
     * ��ȡaccess_tokenֵ
     * @return array ���ذ���access_token,����ʱ�������
     * */
    private function get_token($app_id,$app_key,$code,$callback,$state){
        if($state !== $_SESSION['state']){
            return false;
            exit();
        }
        $url = "https://graph.qq.com/oauth2.0/token";
        $param = array(
            "grant_type"    =>    "authorization_code",
            "client_id"     =>    $app_id,
            "client_secret" =>    $app_key,
            "code"          =>    $code,
            "state"         =>    $state,
            "redirect_uri"  =>    $callback
        );
        $response = $this->get_url($url, $param);
        if($response == false) {
            return false;
        }
        $params = array();
        parse_str($response, $params);
        return $params["access_token"];
    }

    /**
     * ��ȡclient_id �� openid
     * @param $access_token access_token��֤��
     * @return array ���ذ��� openid������
     * */
    private  function get_openid($access_token) {
        $url = "https://graph.qq.com/oauth2.0/me";
        $param = array(
            "access_token"    => $access_token
        );
        $response  = $this->get_url($url, $param);
        if($response == false) {
            return false;
        }
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($response);
        if (isset($user->error) || $user->openid == "") {
            return false;
        }
        return $user->openid;
    }
    /**
     * ��ȡ�û���Ϣ
     * @param $client_id
     * @param $access_token
     * @param $openid
     * @return array �û�����Ϣ����
     * */
    public function get_user_info($app_id,$token,$openid){
        $url = 'https://graph.qq.com/user/get_user_info?oauth_consumer_key='.$app_id.'&access_token='.$token.'&openid='.$openid.'&format=json';
        $str = $this->get_url($url);
        if($str == false) {
            return false;
        }
        $arr = json_decode($str,true);
        return $arr;
    }

    /**
     * ����URL��ַ������callback�õ������ַ���
     * @param $url qq�ṩ��api�ӿڵ�ַ
     * */

    public function callback($app_id, $app_key, $callback) {
        $code = $_GET['code'];
        $state = $_GET['state'];
        $token = $this->get_token($app_id,$app_key,$code,$callback,$state);
        $openid = $this->get_openid($token);
        if(!$token || !$openid) {
            return false;
            exit();
        }
        return array('openid' => $openid, 'token' => $token);
    }


    /*
     * HTTP GET Request
    */
    private  function get_url($url, $param = null) {
        if($param != null) {
            $query = http_build_query($param);
            $url = $url . '?' . $query;
        }
        $ch = curl_init();
        if(stripos($url, "https://") !== false){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"]) == 200) {
            return $content;
        }else{
            echo $status["http_code"];
            return false;
        }
    }

    /*
     * HTTP POST Request
    */
    private  function post_url($url, $params) {
        $ch = curl_init();
        if(stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"]) == 200) {
            return $content;
        } else {
            return false;
        }
    }
}
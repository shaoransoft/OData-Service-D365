<?php
/* Microsoft Dynamics365 Web Service 1.1 */
/* -- Shaoransoft Develop -- */

class ODataServiceD365 {
  private $user;
  private $pwd;
  private $data;

  public function setUsername($user) {
    if (isset($user)) $this->user = $user;
    return $this;
  }

  public function setPassword($pwd) {
    if (isset($pwd)) $this->pwd = $pwd;
    return $this;
  }

  public function setAuth($user, $pwd) {
    setUsername($user)->setPassword($pwd);
    return $this;
  }

  private function getAuth() {
    return "{$this->user}:{$this->pwd}";
  }

  public function getValue() {
    return $this->data['value'];
  }

  public function request($method = 'GET', $url = '', $req = false) {
    if ($this->getAuth()) {
      $curl = curl_init();
      switch (strtoupper($method)) {
        case 'POST':
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($req) curl_setopt($curl, CURLOPT_POSTFIELDS, $req);
          break;
        case 'PUT':
          curl_setopt($curl, CURLOPT_PUT, 1);
          break;
        default:
          if ($req) $url = sprintf("%s?%s", $url, http_build_query($req));
          break;
      }
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, $this->getAuth());
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      $res = curl_exec($curl);
      curl_close($curl);
      if (isset($res)) {
        if ($this->isJson($res)) $res = json_decode($res);
        $this->data = $this->convObjToArray($res);
      }
      else $this->data = ['msg' => 'not response'];
    }
    else $this->data = ['msg' => 'not authorization'];
    return $this;
  }

  private function convObjToArray($data = null) {
    if (is_object($data)) $data = get_object_vars($data);
    return is_array($data) ? array_map([$this, __FUNCTION__], $data) : $data;
  }

  private function isJson($str) {
    json_decode($str);
    return (json_last_error() == JSON_ERROR_NONE);
  }
}
?>

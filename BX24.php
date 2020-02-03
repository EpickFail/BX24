<?
class BX24{

  public $method;
  public $params;
  protected $portal;

  public function __construct($portal, $method, $params){
    $this->portal = $portal;
  }

  public function call($method, $params){
    $this->method = $method;
    $this->params = $params;

    $Data = $this->connect();
    $total = $Data['total'];
    if (!empty($total)){
      if ($total > 50){
        $start = 0;
        while ($start < $total){
          $Params[] = ["$method" => array_merge($params, ['start' => $start])];
          $start += 50;
        }
        $Data = $this->batch($Params);
        return $Data['result'];
      }
      else {
        return $Data;
      }
    }
    else {
      return $Data;
    }
  }

  public function batch($fields){
    $this->method = '/batch.json';
    foreach ($fields as $params){
      foreach ($params as $method => $param){
        $this->params['cmd'][] =  $method."?".http_build_query($param);
      }
    }
    return $this->connect();
  }

  private function connect(){
    usleep(500000);
    $queryUrl = $this->portal.$this->method;

    $queryData = http_build_query($this->params);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData
      ]);
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, true);
      return $result;
  }
}
?>

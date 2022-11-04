<?php

/**
 * BaseControllerClass
 */
class BaseController
{
    /**
     * Default method.
     */
    public function __call($name, $arguments)
    {
        $this->sendOutput(json_encode(['error' => true, 'message' => 'Endpoint does not exist.']), [$this->getResponseType(), 'HTTP/1.1 404 Not Found']);
    }

    /**
     * Get URI elements.
     * 
     * @return array
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        return $uri;
    }

    /**
     * Send API output.
     *
     * @param mixed  $data
     * @param string $httpHeader
     */
    protected function sendOutput($data, $httpHeaders = [])
    {
        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        echo $data;
        exit;
    }

    /**
     * Returns the data response type based on Accept header.
     * @return string
     */
    protected function getResponseType() {
        $headers = getallheaders();
        $responseType = "";

        foreach($headers as $key => $value) {
            if(strtolower($key) == "accept") {
                $responseType = $value;
            }
        }

        if(strpos($responseType, "json") !== false || strpos($responseType, "xml") !== false) {
            $responseType = "Content-Type: " . $responseType;
        } else {
            $responseType = "Content-Type: appication/json";
        }
        
        return $responseType;
    }

    /**
     * Format response data based on Accept header.
     * @param array $responseData The response data array
     * @return string
     */
    protected function formatResponseData($responseData) {
        $responseType = $this->getResponseType();

        switch($responseType) {
            case "Content-Type: application/xml": 
                $xml = new SimpleXMLElement('<root/>');
                $this->arrayToXml($responseData, $xml);

                return $xml->asXML();
            break;
            default: 
                return json_encode($responseData);
            break;
        }
    }

    /**
    * Convert an array to XML
    * @param array $array
    * @param SimpleXMLElement $xml
    */
    protected function arrayToXml($array, &$xml){
       foreach ($array as $key => $value) {
           if(is_int($key)){
               $key = "e";
           }
           if(is_array($value)){
               $label = $xml->addChild($key);
               $this->arrayToXml($value, $label);
           }
           else {
               $xml->addChild($key, $value);
           }
       }
    }
}

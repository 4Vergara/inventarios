<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('SUSERNAME', 'soportte@ccs.org.co'); //sambox de desarrollo
define('SPASSWORD', ''); // ''
define('SECURITY_TOKEN', '6Cel800Di00007000HSOJ888i0000000TXBRt0cAVxroceispAR26FgA7LLU77HCicNXpusnMYbFIyoonxnxsJbBF8lVng9wM7OlHYWNh6z9'); //Valor del token de acceso inicial
define('CLIENT_ID', '3MVG9A2kN3Bn17huH73KWkWVTcQ.3b.H.GByXO5H88Y.qn.Bf3lR.OQ5jkaJrZGJn8nWSqw6BSDTmri35ZmA2o'); //Clave de consumidor
define('CLIENT_SECRET', '21323821782605751586'); //Pregunta secreta de consumidor
define('LOGIN_URI', 'https://na46.salesforce.com'); // cs41 sambox de desarrollo
define('SFVERSION', 'v58.0'); //version sales force
/**
 * Salesforce = SF
 * Esta clase permite conectar y hacer CRUD con el CRM de SF
 *
 * @package Salesfoece
 * @author Fabio Grandas Amado
 * @copyright 2019
 * @version v 1.3
 * @access public
 */
class Salesforce {
    private $curl;
    private $url;
    private $status;
    private $json_response;
    private $data;
    private $session;
    /**
     * Salesforce::login()
     * Conectar con usuario de SF para realizar las consultas al CRM
     *
     * @return
     */
    public function login() {
        $this->url = LOGIN_URI . '/services/oauth2/token';
        $params = 'grant_type=password' . '&client_id=' . CLIENT_ID . '&client_secret=' . CLIENT_SECRET . '&username=' . SUSERNAME . '&password=' . SPASSWORD;
        $this->curl = curl_init($this->url);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        if (ENVIRONMENT !== 'production') {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        $this->json_response = curl_exec($this->curl);
        $this->status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $response = $this->errores();
        if (empty($response['access_token'])) {
            $response['errores']['3'] = 'Error - access token missing from response!';
        }
        if (empty($response['instance_url'])) {
            $response['errores']['4'] = 'Error - instance URL missing from response!';
        }
        curl_close($this->curl);
        $this->data = $response;
        return $response;
    }
    /**
     * Salesforce::getRow()
     * una fila de /sobjects/...
     * ej:$response = $this->sf->getRow('libros__c', 'a2855000000bKWkAAM');
     * @param mixed $pach
     * @param mixed $ide
     * @return
     */
    public function getRow($pach, $ide) {
        $url = '/services/data/' . SFVERSION . '/sobjects/' . $pach . '/' . $ide;
        return $this->ejecutar($url, '', '', 204);
    }
    /**
     * Salesforce::delete()
     * eliminar registros de objeto /sobjects/...
     * ej:$response = $this->sf->delete('libros__c', 'a2855000000bKWkAAM');
     * @param mixed $pach
     * @param mixed $ide
     * @return
     */
    public function delete($pach, $ide) {
        $url = '/services/data/' . SFVERSION . '/sobjects/' . $pach . '/' . $ide;
        return $this->ejecutar($url, '', 'delete', 204);
    }
    /**
     * Salesforce::update()
     * actualizar objeto de /sobjects/...
     * ej:$response = $this->sf->update('libros__c', 'a2855000000bO8uAAE', ['Name' => 'Mi moto uno']);
     * @param mixed $pach objeto
     * @param mixed $ide
     * @param mixed $data
     * @return array
     */
    public function update($pach, $ide, array $data) {
        $content = json_encode($data);
        $url = '/services/data/' . SFVERSION . '/sobjects/' . $pach . '/' . $ide;
        return $this->ejecutar($url, $content, 'update', 204);
    }
    /**
     * Salesforce::insert()
     * insertar datos en el objeto ./sobjects/...
     * $response = $this->sf->insert('motos__c', ['Name' => 'Mi moto uno']);
     * @param mixed $pach
     * @param mixed $data
     * @return array
     */
    public function insert($pach, array $data) {
        $content = json_encode($data);
        $url = '/services/data/' . SFVERSION . '/sobjects/' . $pach . '/';
        return $this->ejecutar($url, $content, 'post', 201);
    }
    /**
     * Salesforce::getsobjects()
     * retorna datos del objeto especifico sobjects/
     * ej:$response = $this->sf->consulta("Profile/00ei0000000VQr5AAG");
     * @param mixed $pach
     * @return array
     */
    public function getsobjects($pach) {
        $url = '/services/data/' . SFVERSION . '/sobjects/' . $pach;
        return $this->ejecutar($url, '', 'header', 200);
    }
    /**
     * Salesforce::consulta()
     * retorna los datos de un objeto
     * ej:$response = $this->sf->consulta("sobjects/Profile/00ei0000000VQr5AAG");
     * @param mixed $pach
     * @return array
     */
    public function consulta($pach) {
        $url = '/services/data/' . SFVERSION . '/' . $pach;
        return $this->ejecutar($url, '', 'header', 200);
    }
    /**
     * Salesforce::query()
     * Datos de una consulta query, 'query?q=...'
     * ej:$response = $this->sf->query('SELECT Id,IsDeleted,Name,OwnerId FROM motos__c LIMIT 20');
     * @param mixed $query
     * @return array
     */
    public function query($query) {
        $url = '/services/data/' . SFVERSION . '/query?q=' . urlencode($query);
        return $this->ejecutar($url, '', 'header', 200);
    }
    /**
     * Salesforce::nextquery()
     * Datos de siguientes de una consulta
     * ej:$response = $this->sf->nextquery('/services/data/v45.0/query/01g0H0000AC53kTQQR-4000');
     * @param mixed $query
     * @return array
     */
    public function nextquery($query) {
        return $this->ejecutar($query, '', 'header', 200);
    }
    public function conectar($session) {
        if (isset($session['salesforce'])) {
            $this->data = $session['salesforce'];
            // console('poqui ln164 lib sf');
        } else {
            $this->data = $this->login();
        }
        //----------------------------
        if (isset($this->data['errores']) || empty($this->data)) {
            $valido = false;
        } else {
            $_SESSION['salesforce'] = $this->data;
            // die(print_r($_SESSION));
            $valido = true;
        }
        return $valido;
    }
    public function getConection() {
        return $this->data;
    }
    /**
     * Salesforce::ejecutar()
     *
     * @param mixed $url ruta o pach de consulta
     * @param mixed $params parametros
     * @param mixed $tipo post update delete
     * @param integer $code codigo de respuesta
     * @return
     */
    private function ejecutar($url, $params, $tipo, $code = 200) {
        $access_token = $this->data['access_token'];
        $token_type = $this->data['token_type'];
        $this->url = $this->data['instance_url'] . $url;
        $this->curl = curl_init($this->url);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("Authorization: $token_type $access_token", 'Content-Type: application/json'));
        if ($tipo == 'post') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        } elseif ($tipo == 'update') {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        } elseif ($tipo == 'delete') {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        if (ENVIRONMENT !== 'production') {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        $this->json_response = curl_exec($this->curl);
        $this->status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $response = $this->errores($code);
        curl_close($this->curl);
        return $response;
    }
    /**
     * Salesforce::errores()
     * retorna un array con los datos o errores
     * @param integer $code
     * @return array
     */
    private function errores($code = 200) {
        if ($this->status != $code) {
            $response['errores']['1'] = "Error: URL $this->url, status $this->status";
            $response['errores']['2'] = "Response: $this->json_response";
        } else {
            $response = json_decode($this->json_response, true);
        }
        return $response;
    }
}

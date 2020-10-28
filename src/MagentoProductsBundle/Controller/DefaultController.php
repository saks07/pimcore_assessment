<?php

namespace MagentoProductsBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use \Pimcore\Model\DataObject;

use \DateTime;

class DefaultController extends FrontendController
{
    private $baseApiUrl = 'http://magentoproject.ddev/rest/all/V1';
    private $adminTokenEndpoint = [ 'url' => '/integration/admin/token', 'method' => 'POST' ];
    private $adminCredentials = [ 'username' => 'admin.magento', 'password' => 'saks1234' ];
    private $productsEndpoint = [ 'url' => '/products', 'method' => 'POST' ];

    /**
     * @Route("/magento_products")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from magento_products');
    }

    private function generateTimestamp() {
        $date = new DateTime();
        return $date->getTimestamp();
    }

    private function performCurl($url, $isPost, $payload, $headers) {
        try {
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, $isPost );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $response = curl_exec($ch);
            if( curl_errno($ch) ) return ['success' => false, 'error_message' => curl_error($ch), 'response' => null];
            $info = curl_getinfo($ch);
            curl_close ($ch);
            return ['success' => true, 'error_message' => '', 'response' => $response];
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    private function preparePayload($array) {
        if( empty($array) ) return [];
        $payload = [];
        foreach($array as $index => $value) {
            $payload[$index]['product']['sku'] = $value->getSku();
            $payload[$index]['product']['name'] = $value->getName();
            $payload[$index]['product']['attribute_set_id'] = intVal($value->getSetid());
            $payload[$index]['product']['price'] = floatVal($value->getPrice());
            $payload[$index]['product']['status'] = intVal($value->getStatus());
            $payload[$index]['product']['visibility'] = intVal($value->getVisibility());
            $payload[$index]['product']['type_id'] = $value->getPtype();
            $payload[$index]['product']['weight'] = $value->getWeight();
            $payload[$index]['product']['extension_attributes']['category_links'][] = ['position' => 0, 'category_id' => '11'];
            $payload[$index]['product']['extension_attributes']['category_links'][] = ['position' => 1, 'category_id' => '12'];
            $payload[$index]['product']['extension_attributes']['category_links'][] = ['position' => 2, 'category_id' => '16'];
            $payload[$index]['product']['extension_attributes']['stock_item']['qty'] = $value->getStockqty();
            $payload[$index]['product']['extension_attributes']['stock_item']['is_in_stock'] = boolVal($value->getStockin());
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'description', 'value' => $value->getDescription() ];
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'tax_class_id', 'value' => $value->getTax() ];
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'material', 'value' => $value->getMaterial() ];
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'pattern', 'value' => $value->getPattern() ];
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'color', 'value' => $value->getColor() ];
            $payload[$index]['product']['custom_attributes'][] = [ 'attribute_code' => 'size', 'value' => $value->getsize() ];
        }
        return json_encode($payload);
    }

    private function prepareRequest($endpoint, $payload, $headers) {
        $buildUrl = $this->baseApiUrl.$endpoint['url'];
        $isPost = $endpoint['method'] === 'POST';
        return $this->performCurl( $buildUrl, $isPost, $payload, $headers );
    }

    private function getAccessToken() {
        $token = $this->prepareRequest( $this->adminTokenEndpoint, json_encode($this->adminCredentials), array( 'Content-Type:application/json' ) );
        $tokenDecode = json_decode($token['response']);
        if( is_object($tokenDecode) ) {
            $token['error_message'] = $tokenDecode['message'];
        } 
        return $token;
    }

    private function performMagentoApiRequest($endpoint, $token, $payload) {
        return $this->prepareRequest( $endpoint, $payload, array( 'Content-Type:application/json', 'Authorization: Bearer '. str_replace('"','',$token) ) );
    }

    public function magentoProductsAction(Request $request) {
        try {
            $entries = new DataObject\Product\Listing();
            $entries->load();

            // prepare payload for POST
            $payload = $this->preparePayload( $entries->getObjects() );

            // prevent post if payload is empty
            if( empty($payload) ) return new Response('Payload is empty! POST prevented.');

            // POST admin token
            $token = $this->getAccessToken();
            if(!$token['success']) return new Response( $token['error_message'] );

            // POST product(s)
            $products = $this->performMagentoApiRequest($this->productsEndpoint, $token['response'], $payload);

            // decode PRODUCTS response
            $productsDecode = json_decode($products['response']);

            // tle sem včeraj opazil eno napakco, sem potem kar komentar napisal. Spodnji primer je POST enega produkta, ne vem kako je struktura response-a ko narediš POST več produktov,
            // sklepam da je object array, v tem primeru bi moralo biti namesto $productsDecode->id => end($productsDecode)->id se pravi ID zadnjega dodanega elementa
            return new Response('Product with ID ' . $productsDecode->id . ' hase been successfully added to products list (timestamp: ' . $this->generateTimestamp() . ')');
        } catch(Exception $e) {
            return new Response( $e->getMessage() );
        }
    }
}

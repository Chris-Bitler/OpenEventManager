<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/8/2018
 * Time: 8:54 AM
 */

namespace Controller;


use Datto\JsonRpc\Server;
use Datto\JsonRpc\Simple\Evaluator;
use Datto\JsonRpc\Simple\Mapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Main controller for the json-rpc api
 * @package Controller
 */
class APIController extends Controller
{

    //TODO: This needs to be unit tested..
    /**
     * Index of the control (/api)
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $content = $this->get('request')->getContent();
        $content = $this->replacePlaceholders($request, $content);
        $server = new Server(new Evaluator(new Mapper('App\\API\\V1')));
        $result = $server->reply($content);
        return new Response($result);
    }

    /**
     * Replace placeholders in the json parameters. This is used for things that can't be
     * determined client-side such as IP address
     * @param Request $request The request for the endpoint
     * @param string $content The content of the json-rpc request
     * @return string The modified request
     */
    private function replacePlaceholders(Request $request, $content)
    {
        $jsonObject = json_decode($content);
        $params = $jsonObject->params;
        if (property_exists($params, 'ip')) {
            $params->ip = $request->getClientIp();
        }

        return json_encode($jsonObject);
    }
}
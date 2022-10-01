<?php
namespace Stanford\CustomCharges;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\CustomCharges\CustomCharges $module */

try {
    if (!defined('SUPER_USER') or SUPER_USER == 0) {
        throw new \Exception('You are not allowed in this page.');
    }
    $action = htmlentities($_GET['action']);
    if ($action == CustomCharges::GET_CHARGES) {
        $query = "select * from " . CustomCharges::REDCAP_ENTITY_CUSTOM_CHARGES . " where project_id = " . intval($_GET['pid']);
        $q = db_query($query);
        $result = [];
        while ($row = db_fetch_assoc($q)) {
            $result[] = $row;
        }
        echo json_encode($result);
    } elseif ($action == CustomCharges::SAVE_CHARGE) {
        if (!isset($_GET['pid'])) {
            throw new \Exception('PID is not available');
        }
        if (!isset($_GET['amount'])) {
            throw new \Exception('Amount is not available');
        }
        $data = array(
            'project_id' => filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT),
            'amount' => filter_var($_GET['amount'], FILTER_SANITIZE_NUMBER_INT),
            'notes' => htmlentities($_GET['notes']),
            'module_prefix' => htmlentities($_GET['module_prefix']),
            'is_recurring' => htmlentities($_GET['is_recurring']),
        );
        $entity = $module->getFactory()->create(CustomCharges::CUSTOM_CHARGES, $data);
        if ($entity) {
            echo json_encode(array('status' => 'success', 'message' => 'record saved correctly'));
        }
    } else {
        throw new \Exception('Unknown Action');
    }
} catch (\LogicException|ClientException|GuzzleException $e) {
    header("Content-type: application/json");
//    http_response_code(404);
    $result['data'] = [];
    echo json_encode($result);
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}

?>

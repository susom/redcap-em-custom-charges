<?php
namespace Stanford\CustomCharges;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\CustomCharges\CustomCharges $module */

try {
    global $Proj;
    if (!defined('SUPER_USER') or SUPER_USER == 0) {
        throw new \Exception('You are not allowed in this page.');
    }

    $action = htmlentities($_GET['action']);
    if ($action == CustomCharges::GET_CHARGES) {
        $query = "select * from " . CustomCharges::REDCAP_ENTITY_CUSTOM_CHARGES . " where project_id = " . intval($_GET['pid']);
        $q = db_query($query);
        $result = [];
        while ($row = db_fetch_assoc($q)) {
            $row['is_recurring'] = $row['is_recurring'] ? 'Yes' : 'No';
            $row['project_id'] = $Proj->project['app_title'];;
            $result[] = $row;
        }
        echo json_encode($result);
    } elseif ($action == CustomCharges::SAVE_CHARGE) {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!isset($_GET['pid'])) {
            throw new \Exception('PID is not available');
        }
        if (!isset($body['amount'])) {
            throw new \Exception('Amount is not available');
        }
        $data = array(
            'project_id' => filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT),
            'amount' => filter_var($body['amount'], FILTER_SANITIZE_NUMBER_INT),
            'notes' => htmlentities($body['notes']),
            'module_prefix' => htmlentities($body['module_prefix']),
            'is_recurring' => htmlentities($body['is_recurring']),
        );
        $entity = $module->getFactory()->create(CustomCharges::CUSTOM_CHARGES, $data);
        if ($entity) {
            echo json_encode(array('status' => 'success', 'message' => 'record saved correctly', 'record' => $data));
        } else {
            throw new \Exception(implode(',', $module->getFactory()->errors));
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

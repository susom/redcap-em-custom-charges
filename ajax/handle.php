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
        $result = $module->getCharges($module->getProjectId());
        echo json_encode(array('status' => 'success', 'records' => $result));
    } elseif ($action == CustomCharges::MODULE_LIST) {
        $records = $module->getModules();
        $result = [array('prefix' => '', 'name' => 'SELECT PREFIX MODULE')];
        foreach ($records as $name => $record) {
            $temp = array_pop($record);
            if (!$temp['module_name']) {
                continue;
            }
            $result[] = array('prefix' => $temp['module_name'], 'name' => $temp['module_display_name'] ?: $temp['module_name'], 'description' => $temp['module_description']);
        }
        echo json_encode(array('status' => 'success', 'records' => $result));
    } elseif ($action == CustomCharges::SAVE_CHARGE) {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!isset($_GET['pid'])) {
            throw new \Exception('PID is not available');
        }
        if (!isset($body['amount'])) {
            throw new \Exception('Amount is not available');
        }
        $module->getR2p2DashboardObject()->getPortal()->setProjectPortalSavedConfig($module->getProjectId());
        $data = array(
            'project_id' => filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT),
            'amount' => filter_var($body['amount'], FILTER_SANITIZE_NUMBER_INT),
            'notes' => htmlentities($body['notes']),
            'module_prefix' => htmlentities($body['module_prefix']),
            'is_recurring' => htmlentities($body['is_recurring']),
            'rma_id' => $module->getR2p2DashboardObject()->getPortal()->getRmaId(),
            'status' => 1,
        );
        // no id means new record
        if (!$body['id']) {
            $entity = $module->getFactory()->create(CustomCharges::CUSTOM_CHARGES, $data);
        } else {
            $entity = $module->getFactory()->getInstance(CustomCharges::CUSTOM_CHARGES, filter_var($body['id'], FILTER_SANITIZE_NUMBER_INT));
            if ($entity->setData($data)) {
                $entity->save();
            }
        }

        if ($entity) {
            echo json_encode(array('status' => 'success', 'message' => 'record saved successfully', 'record' => $data));
        } else {
            throw new \Exception(implode(',', $module->getFactory()->errors));
        }
    } elseif ($action == CustomCharges::DELETE_CHARGE) {
        if (!isset($_GET['id'])) {
            throw new \Exception('ID is not available');
        }
        $result = $module->deleteCharge(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
        if ($result == true) {
            echo json_encode(array('status' => 'success', 'message' => 'record deleted successfully'));
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

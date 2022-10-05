<?php
namespace Stanford\CustomCharges;


use ExternalModules\ExternalModules;
use Stanford\ProjectPortal\ProjectPortal;

require_once "emLoggerTrait.php";

/**
 *
 */
class CustomCharges extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;

    /**
     * @var \REDCapEntity\EntityFactory
     */
    private $factory;

    /**
     *
     */
    const REDCAP_ENTITY_CUSTOM_CHARGES = 'redcap_entity_custom_charges';

    /**
     *
     */
    const CUSTOM_CHARGES = 'custom_charges';

    /**
     *
     */
    const GET_CHARGES = 'get_charges';

    /**
     *
     */
    const SAVE_CHARGE = 'save_charge';

    /**
     *
     */
    const MODULE_LIST = 'get_modules_list';

    /**
     *
     */
    const DELETE_CHARGE = 'delete_charge';

    /**
     *
     */
    const EDIT_CHARGE = 'edit_charge';

    /**
     * @var array
     */
    private $modules = [];

    /**
     *
     */
    private $charges = [];

    /**
     * @var ProjectPortal
     */
    private $r2p2DashboardObject;
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        // we need to make sure this project has RMA in place to be able to generate custom charges;
        if (ExternalModules::getSystemSetting($this->getPrefix(), 'r2p2-dashboard-em') != '') {
            $this->setR2p2DashboardObject(ExternalModules::getModuleInstance(ExternalModules::getSystemSetting($this->getPrefix(), 'r2p2-dashboard-em')));
        }
    }

    public function doesProjectHaveRMA()
    {
        $this->getR2p2DashboardObject()->getPortal()->setProjectPortalSavedConfig($this->getProjectId());
        return $this->getR2p2DashboardObject()->getPortal()->getHasRMA();
    }

    /**
     * @return array
     */
    public function redcap_entity_types()
    {
        $types = [];

        $types[self::CUSTOM_CHARGES] = [
            'label' => 'Custom Charges',
            'label_plural' => 'Custom Charges',
            'icon' => 'home_pencil',
            'properties' => [
                'project_id' => [
                    'name' => 'REDCap Project',
                    'type' => 'project',
                    'required' => true,
                ],
                'module_prefix' => [
                    'name' => 'Optional: Module Prefix',
                    'type' => 'text',
                    'required' => false,
                ],
                'is_recurring' => [
                    'name' => 'Charge recurring?',
                    'type' => 'boolean',
                    'required' => true,
                    'default' => 'true'
                ],
                'amount' => [
                    'name' => 'Amount to be charged',
                    'type' => 'integer',
                    'required' => true,
                ],
                'notes' => [
                    'name' => 'Note',
                    'type' => 'text',
                    'required' => false,
                ],
                'status' => [
                    'name' => 'Charge Status',
                    'type' => 'boolean',
                    'default' => 'true',
                    'required' => false,
                ],
                'rma_id' => [
                    'name' => 'R2P2 RMA id',
                    'type' => 'integer',
                    'default' => 'true',
                    'required' => true,
                ],
            ],
            'special_keys' => [
                'label' => 'project_id', // "name" represents the entity label.
            ],
        ];

        return $types;
    }

    /**
     * @param $project_id
     * @param $link
     * @return mixed|null
     */
    public function redcap_module_link_check_display($project_id, $link)
    {
        //limit the logging link to Super Users
        if ($link['key'] = 'customCharges') {
            if (SUPER_USER) {
                return $link;
            } else {
                return null;
            }
        }
        return $link;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        if (!$this->modules) {
            $this->setModules();
        }
        return $this->modules;
    }

    /**
     */
    public function setModules()
    {
        $param = array(
            'project_id' => $this->getSystemSetting('em-project-id'),
            'return_format' => 'array',
            'events' => $this->framework->getFirstEventId($this->getSystemSetting('em-project-id'))
        );
        $modules = \REDCap::getData($param);
        $this->modules = $modules;
    }


    /**
     * @return ≈
     */
    public function getFactory(): \REDCapEntity\EntityFactory
    {
        if (!$this->factory) {
            $this->setFactory(new \REDCapEntity\EntityFactory);
        }
        return $this->factory;
    }

    /**
     * @param \REDCapEntity\EntityFactory $factory
     */
    public function setFactory(\REDCapEntity\EntityFactory $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * @return array
     */
    public function getCharges($projectId = '')
    {
        if (!$this->charges) {
            $this->setCharges($projectId);
        }
        return $this->charges;
    }

    /**
     */
    public function setCharges($projectId = '')
    {
        global $Proj;
        if ($projectId) {
            $query = "select * from " . CustomCharges::REDCAP_ENTITY_CUSTOM_CHARGES . " where project_id = " . intval($projectId);
        } else {
            $query = "select * from " . CustomCharges::REDCAP_ENTITY_CUSTOM_CHARGES;
        }

        $q = db_query($query);
        $charges = [];
        while ($row = db_fetch_assoc($q)) {
            $row['is_recurring'] = $row['is_recurring'] ? 'Yes' : 'No';
            $row['status'] = $row['status'] ? 'Active' : 'Disabled';
            $row['project_id'] = $Proj->project['app_title'];;
            $row['module_prefix'] = $row['module_prefix'] ?: 'N/A';
            $charges[] = $row;
        }
        $this->charges = $charges;
    }


    /**
     * @throws \Exception
     */
    public function deleteCharge($chargeId)
    {
        $query = "DELETE from " . CustomCharges::REDCAP_ENTITY_CUSTOM_CHARGES . " where id = " . intval($chargeId);

        $q = db_query($query);
        if (!db_error()) {
            return true;
        } else {
            throw new \Exception(db_error());
        }
    }

    /**
     * @return ProjectPortal
     */
    public function getR2p2DashboardObject(): ProjectPortal
    {
        return $this->r2p2DashboardObject;
    }

    /**
     * @param ProjectPortal $r2p2DashboardObject
     */
    public function setR2p2DashboardObject(ProjectPortal $r2p2DashboardObject): void
    {
        $this->r2p2DashboardObject = $r2p2DashboardObject;
    }


}

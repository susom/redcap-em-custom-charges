<?php
namespace Stanford\CustomCharges;


require_once "emLoggerTrait.php";

class CustomCharges extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;

    /**
     * @var \REDCapEntity\EntityFactory
     */
    private $factory;

    const REDCAP_ENTITY_CUSTOM_CHARGES = 'redcap_entity_custom_charges';

    const CUSTOM_CHARGES = 'custom_charges';

    const GET_CHARGES = 'get_charges';

    const SAVE_CHARGE = 'save_charge';

    public function __construct()
    {
        parent::__construct();
        // Other code to run when object is instantiated
    }

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
                    'type' => 'boolean',
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
            ],
            'special_keys' => [
                'label' => 'project_id', // "name" represents the entity label.
            ],
        ];

        return $types;
    }

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

}

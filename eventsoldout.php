<?php

require_once 'eventsoldout.civix.php';
use CRM_Eventsoldout_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventsoldout_civicrm_config(&$config) {
  _eventsoldout_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventsoldout_civicrm_xmlMenu(&$files) {
  _eventsoldout_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventsoldout_civicrm_install() {
  _eventsoldout_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function eventsoldout_civicrm_postInstall() {
  _eventsoldout_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventsoldout_civicrm_uninstall() {
  _eventsoldout_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventsoldout_civicrm_enable() {
  _eventsoldout_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventsoldout_civicrm_disable() {
  _eventsoldout_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventsoldout_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventsoldout_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventsoldout_civicrm_managed(&$entities) {
  _eventsoldout_civix_civicrm_managed($entities);$entities[] = [
    'module' => 'org.civicrm.eventsoldout',
    'name' => 'eventsoldout_customGroup',
    'entity' => 'CustomGroup',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'name' => 'eventsoldout_customGroup',
      'title' => ts('Event Sold out'),
      'extends' => 'Event',
      'style' => 'Inline',
      'is_active' => 1,
      'is_reserved' => 1,
      'is_public' => 0,
    ],
  ];
  $entities[] = [
    'module' => 'org.civicrm.eventsoldout',
    'name' => 'eventsoldout_customField',
    'entity' => 'CustomField',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'name' => 'eventsoldout_override_soldout',
      'label' => ts('Disable sold out option for backoffice?'),
      'data_type' => 'Boolean',
      'html_type' => 'Radio',
      'is_active' => 1,
      'text_length' => 255,
      'default_value' => 1,
      'custom_group_id' => 'eventsoldout_customGroup',
      'help_post' => ts('If disabled, CiviCRM will allow to add seats.'),
    ],
  ];
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventsoldout_civicrm_caseTypes(&$caseTypes) {
  _eventsoldout_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function eventsoldout_civicrm_angularModules(&$angularModules) {
  _eventsoldout_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventsoldout_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventsoldout_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function eventsoldout_civicrm_entityTypes(&$entityTypes) {
  _eventsoldout_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function eventsoldout_civicrm_buildForm($formName, &$form) {
  if ('CRM_Custom_Form_CustomDataByType' == $formName
    && $form->getVar('_type') == 'Event'
  ) {
    CRM_Core_Resources::singleton()->addStyle(
      '.custom-group-eventsoldout_customGroup { display: none !important; }'
    );
  }

  if ('CRM_Event_Form_ManageEvent_Fee' == $formName) {
    $snippet = CRM_Utils_Request::retrieve('snippet', 'String');
    if (empty($snippet)) {
      return;
    }
    try {
      $customField = civicrm_api3('CustomField', 'getsingle', [
        'return' => ["id", 'label'],
        'custom_group_id' => "eventsoldout_customGroup",
        'name' => "eventsoldout_override_soldout",
      ]);
      $form->assign('eventsoldout', "eventsoldout_override_soldout");
      $form->addYesNo(
        "eventsoldout_override_soldout",
        $customField['label']
      );
      CRM_Core_Region::instance('page-body')->add([
        'template' => 'CRM/EventSoldOut/Form/common.tpl',
      ]);

      $default = [
        "eventsoldout_override_soldout" => 1,
      ];
      if ($form->getVar('_id')) {
        $default["eventsoldout_override_soldout"] = _eventsoldout_civicrm_eventsoldout(
          $form->getVar('_id'),
          $customField['id']
        );
      }
      $form->setDefaults($default);
    }
    catch (Exception $e) {
      //ignore exception.
    }
  }
  if ('CRM_Event_Form_Participant' == $formName) {
    CRM_EventSoldOut_Utils::freeSoldOutOptions($form);
  }

}

/**
 * Get value of custom field Event Sold out.
 */
function _eventsoldout_civicrm_eventsoldout($eventId, $customFieldId = NULL) {
  if (empty($customFieldId)) {
    $customFieldId = _eventsoldout_civicrm_getCustomFieldId();
  }
  if ($customFieldId) {
    try {
      return civicrm_api3('Event', 'getvalue', [
        'return' => "custom_{$customFieldId}",
        'id' => $eventId,
      ]);
    }
    catch (Exception $e) {
      // ignore exception
    }
  }
  return 1;
}

/**
 * Get custom field id of Event Sold out.
 */
function _eventsoldout_civicrm_getCustomFieldId() {
  try {
    return civicrm_api3('CustomField', 'getvalue', [
      'return' => "id",
      'custom_group_id' => "eventsoldout_customGroup",
      'name' => "eventsoldout_override_soldout",
    ]);
  }
  catch (Exception $e) {
    // ignore exception
  }
  return NULL;
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function eventsoldout_civicrm_postProcess($formName, &$form) {
  if ('CRM_Event_Form_ManageEvent_Fee' == $formName) {
    $submitValues = $form->_submitValues;
    if (isset($submitValues['eventsoldout_override_soldout'])) {
      $customFieldId = _eventsoldout_civicrm_getCustomFieldId();
      if (empty($customFieldId)) {
        return;
      }
      civicrm_api3('Event', 'create', [
        "custom_{$customFieldId}" => $submitValues['eventsoldout_override_soldout'],
        'id' => $form->getVar('_id'),
        'is_template' => CRM_Utils_Array::value('is_template', $submitValues),
      ]);
      $form->ajaxResponse['updateTabs']['#tab_settings'] = 1;
    }
  }
}

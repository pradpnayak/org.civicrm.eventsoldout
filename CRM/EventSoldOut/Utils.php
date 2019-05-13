<?php

class CRM_EventSoldOut_Utils {

  /**
   * Freeze sold out elements.
   *
   * @param object $form
   */
  public static function freeSoldOutOptions(&$form) {
    if (empty($form->_showFeeBlock)) {
      return;
    }
    self::formatFieldsForOptionFull($form);

    foreach ($form->_feeBlock as $field) {
      $optionFullIds = CRM_Utils_Array::value('option_full_ids', $field, []);
      $type = $field['html_type'];
      $fieldName = "price_{$field['id']}";
      if (!empty($optionFullIds)
        && (count($field['options']) == count($optionFullIds))
      ) {
        $fieldId = array_search($fieldName, $form->_required);
        if ($fieldId !== FALSE) {
          unset($form->_required[$fieldId]);
        }
      }
      foreach ($field['options'] as $option) {
        if (in_array($option['id'], $optionFullIds)) {
          $element = $form->_elements[$form->_elementIndex[$fieldName]];
          self::freezeElement($element, $type, $option['label'], $option['id']);
          if (!isset($form->_defaultValues[$fieldName])) {
            continue;
          }
          if ($type != 'CheckBox') {
            if ($form->_defaultValues[$fieldName] == $option['id']) {
              unset($form->_defaultValues[$fieldName]);
            }
          }
          else {
            unset($form->_defaultValues[$fieldName][$option['id']]);
          }
        }
      }
    }
    $form->setDefaults($form->_defaultValues);
  }

  /**
   * Freeze element.
   *
   * @param object $element
   * @param string $type
   * @param string $label
   * @param int $fieldId
   */
  public static function freezeElement(&$element, $type, $label, $fieldId) {
    $valueField = NULL;
    switch ($type) {
      case 'Text':
        $element->freeze();
        $element->setLabel($label . '&nbsp;<span class="sold-out-option">' . ts('Sold out') . '</span>');
        break;

      case 'CheckBox':
        $valueField = 'id';
      case 'Radio':
        if (empty($valueField)) {
          $valueField = 'value';
        }
        foreach ($element->_elements as &$option) {
          $value = $option->_attributes[$valueField];
          if ($value == $fieldId) {
            unset($option->_attributes['checked']);
            $option->freeze();
            $option->setText('<span class="sold-out-option">' . $option->getText() . '&nbsp;(' . ts('Sold out') . ')</span>');
            break;
          }
        }
        break;

      case 'Select':
        foreach ($element->_options as &$option) {
          if ($option['attr']['value'] == $fieldId) {
            $option['attr']['value'] = 'crm_disabled_opt-' . $fieldId;
            $option['text'] .= ' (' . ts('Sold out') . ')';
            break;
          }
        }
        break;
    }
  }

  /**
   * @param CRM_Event_Form_Participant $form
   */
  public static function formatFieldsForOptionFull(&$form) {
    $priceSet = $form->get('priceSet');
    $priceSetId = $form->get('priceSetId');
    $defaultPricefieldIds = [];
    if (!empty($form->_values['line_items'])) {
      foreach ($form->_values['line_items'] as $lineItem) {
        $defaultPricefieldIds[] = $lineItem['price_field_value_id'];
      }
    }
    if (!$priceSetId ||
      !is_array($priceSet) ||
      empty($priceSet) || empty($priceSet['optionsMaxValueTotal'])
    ) {
      return;
    }

    $skipParticipants = $formattedPriceSetDefaults = [];
    if (!empty($form->_allowConfirmation) && (isset($form->_pId) || isset($form->_additionalParticipantId))) {
      $participantId = isset($form->_pId) ? $form->_pId : $form->_additionalParticipantId;
      $pricesetDefaults = CRM_Event_Form_EventFees::setDefaultPriceSet($participantId,
        $form->_eventId
      );
      // modify options full to respect the selected fields
      // options on confirmation.
      $formattedPriceSetDefaults = CRM_Event_Form_Registration_Register::formatPriceSetParams($form, $pricesetDefaults);

      // to skip current registered participants fields option count on confirmation.
      $skipParticipants[] = $form->_participantId;
      if (!empty($form->_additionalParticipantIds)) {
        $skipParticipants = array_merge($skipParticipants, $form->_additionalParticipantIds);
      }
    }

    $className = CRM_Utils_System::getClassName($form);

    //get the current price event price set options count.
    $currentOptionsCount = CRM_Event_Form_Registration_Register::getPriceSetOptionCount($form);
    $recordedOptionsCount = CRM_Event_BAO_Participant::priceSetOptionsCount($form->_eventId, $skipParticipants);
    $optionFullTotalAmount = 0;
    $currentParticipantNo = (int) substr($form->getVar('_name'), 12);
    foreach ($form->_feeBlock as & $field) {
      $optionFullIds = [];
      $fieldId = $field['id'];
      if (!is_array($field['options'])) {
        continue;
      }
      foreach ($field['options'] as & $option) {
        $optId = $option['id'];
        $count = CRM_Utils_Array::value('count', $option, 0);
        $maxValue = CRM_Utils_Array::value('max_value', $option, 0);
        $dbTotalCount = CRM_Utils_Array::value($optId, $recordedOptionsCount, 0);
        $currentTotalCount = CRM_Utils_Array::value($optId, $currentOptionsCount, 0);

        $totalCount = $currentTotalCount + $dbTotalCount;
        $isFull = FALSE;
        if ($maxValue &&
          (($totalCount >= $maxValue) &&
          (empty($form->_lineItem[$currentParticipantNo][$optId]['price_field_id']) || $dbTotalCount >= $maxValue))
        ) {
          $isFull = TRUE;
          $optionFullIds[$optId] = $optId;
          if ($field['html_type'] != 'Select') {
            if (in_array($optId, $defaultPricefieldIds)) {
              $optionFullTotalAmount += CRM_Utils_Array::value('amount', $option);
            }
          }
          else {
            if (!empty($defaultPricefieldIds) && in_array($optId, $defaultPricefieldIds)) {
              unset($optionFullIds[$optId]);
            }
          }
        }
        //here option is not full,
        //but we don't want to allow participant to increase
        //seats at the time of re-walking registration.
        if ($count &&
          !empty($form->_allowConfirmation) &&
          !empty($formattedPriceSetDefaults)
        ) {
          if (empty($formattedPriceSetDefaults["price_{$field}"]) || empty($formattedPriceSetDefaults["price_{$fieldId}"][$optId])) {
            $optionFullIds[$optId] = $optId;
            $isFull = TRUE;
          }
        }
        $option['is_full'] = $isFull;
        $option['db_total_count'] = $dbTotalCount;
        $option['total_option_count'] = $dbTotalCount + $currentTotalCount;
      }

      //finally get option ids in.
      $field['option_full_ids'] = $optionFullIds;
    }
    $form->assign('optionFullTotalAmount', $optionFullTotalAmount);
  }

}

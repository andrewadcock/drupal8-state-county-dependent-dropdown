<?php

namespace Drupal\county_selector\Plugin\Field\FieldWidget;

use Drupal\Console\Core\Utils\NestedArray;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\county_selector\Plugin\Field\FieldType\AreaItem;
use PhpParser\Node\Expr\ArrayItem;
use Symfony\Component\Validator\ConstraintViolationInterface;


/**
 * Simple form widget for a recipe ingredient line.
 *
 * @FieldWidget(
 *   id = "state_county_selector_simple",
 *   module = "state_county",
 *   label = @Translation("State County Selector Line"),
 *   field_types = {
 *     "state_county"
 *   }
 * )
 */
class AreaWidget extends WidgetBase
{

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $parent = $items->getFieldDefinition()->getName();
    $inputs = $form_state->getUserInput();

    if ($inputs) {
      $input = $inputs[$parent][$delta];
      $state_val = $input['state'] ?? (isset($items[$delta]->state) ? $items[$delta]->state : NULL);
    }
    else {
      $state_val = $items[$delta]->state ?? NULL;
    }

    $wrapper = 'area-wrapper' . $delta;


    $element['state'] = [
      '#type' => 'select',
      '#required' => FALSE,
      '#default_value' => $state_val,
      '#tree' => TRUE,
      '#title' => $this
        ->t('Select state'),
      '#options' => ['' => $this->t('- None -')] + AreaItem::allowedStateValues(),
      '#ajax' => array(
        'callback' => [$this, 'updateCounties'],
        'event' => 'change',
        'wrapper' => $wrapper,
        '#limit_validation_errors' => array(),
        'progress' => array(
          'type' => 'throbber',
          'message' => NULL,
        ),
      ),
    ];

    $county_val = isset($items[$delta]->county) ? $items[$delta]->county : NULL;

    $allowedCountyValues = AreaItem::allowedCountyValues($state_val);

    $element['county'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#tree' => TRUE,
      '#title' => $this
        ->t('Select County'),
      '#description' => $this->t('<a href="#" class="county-select-all">Select All</a><br>To select multiple hold CTRL and left click on your selection'),
      '#options' => ['' => $this->t('- None -')] + $allowedCountyValues,
      '#default_value' => array_key_exists($county_val, $allowedCountyValues) ? $county_val : NULL,
      '#attributes' => ['class' => ['county-selector-county']],
    ];


    $element['city'] = [
      '#type' => 'textfield',
      '#required' => FALSE,
      '#size' => 20,
      '#tree' => TRUE,
      '#title' => 'City (Cities)',
      '#placeholder' => 'City',
      '#maxlength' => 255,
      '#default_value' => isset($items[$delta]->city) ? $items[$delta]->city : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'container-inline';
    $element['#attributes']['class'][] = 'county-selector-area-elements';
    $element['#attached']['library'][] = 'county_selector/county_selector_area';
    $element['#prefix'] = '<div id="' . $wrapper . '">';
    $element['#suffix'] = '</div>';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return isset($violation->arrayPropertyPath[0]) ? $element[$violation->arrayPropertyPath[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['state'] === '') {
        $values[$delta]['state'] = NULL;
      }
      if ($value['county'] === '') {
        $values[$delta]['county'] = NULL;
      } else {

        $counties = implode(', ', $value['county']);

        $values[$delta]['county'] = $counties;

      }
      if ($value['city'] === '') {
        $values[$delta]['city'] = NULL;
      }
    }
    return $values;
  }

  public function updateCounties(array $form, FormStateInterface $form_state)
  {

    $triggeringElement = $form_state->getTriggeringElement();
    $parent = array_slice($triggeringElement['#array_parents'], 0, -1);
    return NestedArray::getValue($form, $parent);

  }

}

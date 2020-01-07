<?php
namespace Drupal\county_selector\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\county_selector\Plugin\Field\FieldType\AreaItem;
/**
 * Defines the 'county_selector_area' field widget.
 *
 * @FieldFormatter(
 *   id = "state_county_selector_line",
 *   label = @Translation("Geographical Area"),
 *   field_types = {"state_county"},
 * )
 */
class AreaDefaultFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      if ($item->state) {
        $allowed_values = AreaItem::allowedStateValues();
        $element[$delta]['state'] = [
          '#type' => 'item',
          '#title' => $this->t('State'),
          '#markup' => $allowed_values[$item->state],
        ];

        if ($item->county) {
          $allowed_values = AreaItem::allowedCountyValues($item->state);
          $element[$delta]['county'] = [
            '#type' => 'item',
            '#title' => $this->t('County'),
            '#markup' => $item->county,
          ];
        }
        if ($item->city) {
          $element[$delta]['city'] = [
            '#type' => 'item',
            '#title' => $this->t('City'),
            '#markup' => $item->city,
          ];
        }
      }
    }
    return $element;
  }

}

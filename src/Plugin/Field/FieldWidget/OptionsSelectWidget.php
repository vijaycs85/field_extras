<?php

namespace Drupal\field_extras\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget as OptionsSelectWidgetCore;
/**
 * Plugin implementation of the 'options_select' widget.
 *
 * @FieldWidget(
 *   id = "field_extras_options_select",
 *   label = @Translation("Extras: Select list"),
 *   field_types = {
 *     "entity_reference",
 *     "list_integer",
 *     "list_float",
 *     "list_string"
 *   },
 *   multiple_values = TRUE
 * )
 */
class OptionsSelectWidget extends OptionsSelectWidgetCore {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'view_mode' => 'default',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $element['view_mode'] = [
      '#type' => 'select',
      '#title'  => $this->t('View mode'),
      '#options' => $this->getViewModes(),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $view_modes = $this->getViewModes();
    $summary[] = t('View mode: @view_mode', ['@view_mode' => $view_modes[$this->getSetting('view_mode')]]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $referenced_entities = $items->referencedEntities();
    $referenced_entity = isset($referenced_entities[$delta]) ? $referenced_entities[$delta] : NULL;
    if ($referenced_entity) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($referenced_entity->getEntityTypeId());
      $preview = $view_builder->view($referenced_entity, 'inline');
      $element['#suffix'] = render($preview);
    }

    return $element;
  }

  /**
   * Returns the view modes.
   *
   * @return array
   *   List of options.
   */
  protected function getViewModes() {
    // @todo: replace with dynamic view modes.
    return [
      'default' => $this->t('Default'),
      'inline' => 'Inline',
    ];
  }

}

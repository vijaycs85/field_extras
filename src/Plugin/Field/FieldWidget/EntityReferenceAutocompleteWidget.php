<?php

namespace Drupal\field_extras\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget as EntityReferenceAutocompleteWidgetCore;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'entity_reference_select' widget.
 *
 * @FieldWidget(
 *   id = "field_extras_entity_reference_autocomplete",
 *   label = @Translation("Extras: Autocomplete"),
 *   description = @Translation("A select item field."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidgetCore {

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
      '#title'  => $this->t('Preview mode'),
      '#options' => $this->getViewModes(),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $views = $this->getViewModes();
    $summary[] = t('Preview mode: @view_mode', ['@view_mode' => $views[$this->getSetting('view_mode')]]);
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
      $element['preview'] = $view_builder->view($referenced_entity, 'inline');
      $element['preview']['#weight'] = 2;
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

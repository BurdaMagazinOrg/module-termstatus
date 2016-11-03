<?php

namespace Drupal\termstatus;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\termstatus\Plugin\Field\TaxonomyTermStatusFieldItemList;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manipulates entity type information.
 *
 * This class contains primarily bridged hooks for compile-time or
 * cache-clear-time hooks. Runtime hooks should be placed in EntityOperations.
 */
class EntityTypeInfo implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * Adds base field info to an entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   Entity type for adding base fields to.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   New fields added by termstatus.
   */
  public function entityBaseFieldInfo(EntityTypeInterface $entity_type) {

    if ($entity_type->id() != 'taxonomy_term') {
      return [];
    }

    $fields = [];
    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel($this->t('Moderation state'))
      ->setDescription(t('The moderation state of this piece of content.'))
      ->setComputed(TRUE)
      ->setClass(TaxonomyTermStatusFieldItemList::class)
      ->setSetting('target_type', 'taxonomy_term_status')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'hidden',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setTranslatable(FALSE);

    return $fields;
  }

}

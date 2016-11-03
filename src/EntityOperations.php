<?php

namespace Drupal\termstatus;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class for reacting to entity events.
 */
class EntityOperations implements ContainerInjectionInterface {

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EntityOperations object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Hook bridge.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that was just saved.
   *
   * @see hook_entity_insert()
   */
  public function entityInsert(EntityInterface $entity) {
    if ($entity instanceof Term) {
      $this->updateOrCreateFromEntity($entity);
    }
  }

  /**
   * Hook bridge.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that was just saved.
   *
   * @see hook_entity_update()
   */
  public function entityUpdate(EntityInterface $entity) {
    if ($entity instanceof Term) {
      $this->updateOrCreateFromEntity($entity);
    }
  }

  /**
   * Creates or updates the status of a term.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to update or create a status for.
   */
  protected function updateOrCreateFromEntity(EntityInterface $entity) {
    $status = $entity->status->value;

    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    if ($status == NULL) {
      $status = 0;
    }

    $entity_id = $entity->id();

    $storage = $this->entityTypeManager->getStorage('taxonomy_term_status');
    $entities = $storage->loadByProperties([
      'tid' => $entity_id,
    ]);

    /** @var TaxonomyTermStatusInterface $state */
    $state = reset($entities);
    if (!$state) {
      $state = $storage->create([
        'tid' => $entity_id,
      ]);
    }
    else {
      // Create a new revision.
      $state->setNewRevision(TRUE);
    }

    $state->set('status', $status);
    $state->save();
  }

}

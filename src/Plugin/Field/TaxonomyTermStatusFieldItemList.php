<?php

namespace Drupal\termstatus\Plugin\Field;

use Drupal\Core\Field\FieldItemList;

/**
 * A computed field that provides a taxonomy status.
 *
 * It links terms to a status entity.
 */
class TaxonomyTermStatusFieldItemList extends FieldItemList {

  /**
   * Gets the current status of a term.
   *
   * @return int
   *   The status of a term.
   */
  protected function getStatus() {

    $entity = $this->getEntity();

    if ($entity->id()) {
      $revisions = \Drupal::service('entity.query')->get('taxonomy_term_status')
        ->condition('tid', $entity->id())
        ->allRevisions()
        ->sort('revision_id', 'DESC')
        ->execute();

      if ($revision_to_load = key($revisions)) {
        /** @var \Drupal\termstatus\TaxonomyTermStatusInterface $status */
        $status = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term_status')
          ->loadRevision($revision_to_load);

        return $status->get('status')->value;
      }
    }

    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function get($index) {
    if ($index !== 0) {
      throw new \InvalidArgumentException('An entity can not have multiple status at the same time.');
    }
    // Compute the value of the moderation state.
    if (!isset($this->list[$index]) || $this->list[$index]->isEmpty()) {
      $status = $this->getStatus();
      // Do not store NULL values in the static cache.
      if ($status !== NULL) {
        $this->list[$index] = $this->createItem($index, ['value' => $status]);
      }
    }

    return isset($this->list[$index]) ? $this->list[$index] : NULL;
  }

}

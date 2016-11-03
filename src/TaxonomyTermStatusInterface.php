<?php

namespace Drupal\termstatus;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Taxonomy term status entities.
 *
 * @ingroup termstatus
 */
interface TaxonomyTermStatusInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Returns the Taxonomy term status published status indicator.
   *
   * Unpublished Taxonomy term status are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Taxonomy term status is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Taxonomy term status.
   *
   * @param bool $published
   *   TRUE to set this Taxonomy term status to published,
   *   FALSE to set it to unpublished.
   *
   * @return \Drupal\termstatus\TaxonomyTermStatusInterface
   *   The called Taxonomy term status entity.
   */
  public function setPublished($published);

}

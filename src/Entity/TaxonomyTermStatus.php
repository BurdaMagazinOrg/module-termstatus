<?php

namespace Drupal\termstatus\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\termstatus\TaxonomyTermStatusInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Taxonomy term status entity.
 *
 * @ingroup termstatus
 *
 * @ContentEntityType(
 *   id = "taxonomy_term_status",
 *   label = @Translation("Taxonomy term status"),
 *   base_table = "taxonomy_term_status",
 *   revision_table = "taxonomy_term_status_revision",
 *   admin_permission = "administer taxonomy term status entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "tid" = "tid",
 *     "uid" = "uid",
 *     "status" = "status",
 *   },
 * )
 */
class TaxonomyTermStatus extends ContentEntityBase implements TaxonomyTermStatusInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['tid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Term ID'))
      ->setDescription(t('The ID of the term this is for.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The username of the entity creator.'))
      ->setSetting('target_type', 'user')
      ->setTranslatable(FALSE)
      ->setRevisionable(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Taxonomy term status is published.'))
      ->setDefaultValue(TRUE);

    return $fields;
  }

}

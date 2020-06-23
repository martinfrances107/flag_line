<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\flag_line\Entity\RunEntityInterface;

/**
 * Defines the storage handler class for Run Entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Run Entity entities.
 *
 * @ingroup flag_line
 */
class RunEntityStorage extends SqlContentEntityStorage implements RunEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(RunEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {run_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {run_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(RunEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {run_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('run_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}

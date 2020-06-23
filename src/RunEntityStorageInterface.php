<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface RunEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Run Entity revision IDs for a specific Run Entity.
   *
   * @param \Drupal\flag_line\Entity\RunEntityInterface $entity
   *   The Run Entity entity.
   *
   * @return int[]
   *   Run Entity revision IDs (in ascending order).
   */
  public function revisionIds(RunEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Run Entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Run Entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\flag_line\Entity\RunEntityInterface $entity
   *   The Run Entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(RunEntityInterface $entity);

  /**
   * Unsets the language for all Run Entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}

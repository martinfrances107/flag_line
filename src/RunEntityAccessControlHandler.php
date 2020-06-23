<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Run Entity entity.
 *
 * @see \Drupal\flag_line\Entity\RunEntity.
 */
class RunEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\flag_line\Entity\RunEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished run entity entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published run entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit run entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete run entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add run entity entities');
  }

}

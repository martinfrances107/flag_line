<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainEntityAccessControlHandler.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Train entity entity.
 *
 * @see \Drupal\flag_line\Entity\TrainEntity.
 */
class TrainEntityAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view train entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit train entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete train entity entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add train entity entities');
  }

}

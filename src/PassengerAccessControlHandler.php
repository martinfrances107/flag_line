<?php

/**
 * @file
 * Contains Drupal\flag_line\PassengerAccessControlHandler.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Passenger entity.
 *
 * @see \Drupal\flag_line\Entity\Passenger.
 */
class PassengerAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view passenger entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit passenger entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete passenger entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add passenger entities');
  }

}

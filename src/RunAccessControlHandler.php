<?php
namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Run entity.
 *
 * @see \Drupal\flag_line\Entity\Run.
 */
class RunAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view run entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit run entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete run entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add run entities');
  }

}

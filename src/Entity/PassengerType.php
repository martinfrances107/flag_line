<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\PassengerType.
 */

namespace Drupal\flag_line\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\flag_line\PassengerTypeInterface;

/**
 * Defines the Passenger type entity.
 *
 * @ConfigEntityType(
 *   id = "passenger_type",
 *   label = @Translation("Passenger type"),
 *   handlers = {
 *     "list_builder" = "Drupal\flag_line\PassengerTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\flag_line\Form\PassengerTypeForm",
 *       "edit" = "Drupal\flag_line\Form\PassengerTypeForm",
 *       "delete" = "Drupal\flag_line\Form\PassengerTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "passenger_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/passenger_type/{passenger_type}",
 *     "edit-form" = "/admin/structure/passenger_type/{passenger_type}/edit",
 *     "delete-form" = "/admin/structure/passenger_type/{passenger_type}/delete",
 *     "collection" = "/admin/structure/visibility_group"
 *   }
 * )
 */
class PassengerType extends ConfigEntityBase implements PassengerTypeInterface {
  /**
   * The Passenger type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Passenger type label.
   *
   * @var string
   */
  protected $label;

}

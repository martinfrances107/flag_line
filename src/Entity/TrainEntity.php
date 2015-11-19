<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\TrainEntity.
 */

namespace Drupal\flag_line\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\flag_line\TrainEntityInterface;
use Drupal\user\UserInterface;
use Drupal\flag_line\PassengerInterface;

/**
 * Defines the Train entity entity.
 *
 * @ingroup flag_line
 *
 * @ContentEntityType(
 *   id = "train_entity",
 *   label = @Translation("Train entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\flag_line\TrainEntityListBuilder",
 *     "views_data" = "Drupal\flag_line\Entity\TrainEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\flag_line\Entity\Form\TrainEntityForm",
 *       "add" = "Drupal\flag_line\Entity\Form\TrainEntityForm",
 *       "edit" = "Drupal\flag_line\Entity\Form\TrainEntityForm",
 *       "delete" = "Drupal\flag_line\Entity\Form\TrainEntityDeleteForm",
 *     },
 *     "access" = "Drupal\flag_line\TrainEntityAccessControlHandler",
 *   },
 *   base_table = "train_entity",
 *   admin_permission = "administer TrainEntity entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/train_entity/{train_entity}",
 *     "edit-form" = "/admin/train_entity/{train_entity}/edit",
 *     "delete-form" = "/admin/train_entity/{train_entity}/delete"
 *   },
 *   field_ui_base_route = "train_entity.settings"
 * )
 */
class TrainEntity extends ContentEntityBase implements TrainEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addPassenger(PassengerInterface $passenger) {
    $passenger
      ->setBoarded($this->id)
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function removePassengers($station_id) {
    // Are there any passenger getting off at the station?
    if (count($this->passengerLists[$station_id]) == 0) {
      return [];
    }

    // Replace with an empty passenger list.
    $extracted = array_splice($this->passengerLists, $station_id, 1, [[]]);
    $list_count = count($extracted);
    if ($list_count != 1) {
      throw new \Exception($this->t("Train:removePassengers() - Must find one passenger list! Found $list_count."));
    }

    /* $extracted is an array of passenger lists -
     *  Only one list is ever extracted!
     */
    $passengers_out = array_pop($extracted);

    return $passengers_out;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Train entity entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Train entity entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Train entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Train service.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('Out of Service')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['passeneger_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Train entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Train entity entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}

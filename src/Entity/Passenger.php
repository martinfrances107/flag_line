<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\Passenger.
 */

namespace Drupal\flag_line\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\flag_line\PassengerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Passenger entity.
 *
 * @ingroup flag_line
 *
 * @ContentEntityType(
 *   id = "passenger",
 *   label = @Translation("Passenger"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\flag_line\PassengerListBuilder",
 *     "views_data" = "Drupal\flag_line\Entity\PassengerViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\flag_line\Entity\Form\PassengerForm",
 *       "add" = "Drupal\flag_line\Entity\Form\PassengerForm",
 *       "edit" = "Drupal\flag_line\Entity\Form\PassengerForm",
 *       "delete" = "Drupal\flag_line\Entity\Form\PassengerDeleteForm",
 *     },
 *     "access" = "Drupal\flag_line\PassengerAccessControlHandler",
 *   },
 *   base_table = "passenger",
 *   admin_permission = "administer Passenger entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/passenger/{passenger}",
 *     "edit-form" = "/admin/passenger/{passenger}/edit",
 *     "delete-form" = "/admin/passenger/{passenger}/delete"
 *   },
 *   field_ui_base_route = "passenger.settings"
 * )
 */
class Passenger extends ContentEntityBase implements PassengerInterface {

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
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getSrc() {
    return $this->get('src')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getDst() {
    return $this->get('dst')->value;
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
  public function hasAlighted() {
    return (bool) $this->get('alighted')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function hasBoarded() {
    return (bool) $this->get('boarded')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isMovingUpwards() {
    return (bool) $this->get('upwards')->value;
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
  public function setAlighted() {
    $this->set('alighted', TRUE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setBoarded(NodeInterface $train) {
    $this->set('boarded', TRUE);
    $this->set('train_id', $train->id());
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Passenger entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Passenger entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Passenger entity.'))
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
      ->setDescription(t('The name of the Run entity.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['run_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Run'))
      ->setDescription(t('The run which created this passenger.'))
      ->setSetting('target_type', 'run')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'run',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['train_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Train'))
      ->setDescription(t('The train used.'))
      //->setSetting('target_type', 'train_entity')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'run',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['src'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Departing'))
      ->setDescription(t('The ID of the initial station.'))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ));

    $fields['dst'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Arriving'))
      ->setDescription(t('The ID of the final station.'))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -7,
      ));

    $fields['upwards'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Upwards'))
      ->setDescription(t('Is the passenger moving up the line?'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -8,
      ));

    $fields['boarded'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Boarded'))
      ->setDescription(t('Has the passenger gotten on a train?'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -8,
      ));

    $fields['alighted'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Alighted'))
      ->setDescription(t('Has the passenger gotten off the train?'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -9,
      ));

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Passenger entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}

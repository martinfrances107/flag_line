<?php

namespace Drupal\flag_line\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\flag_line\RunInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Run entity.
 *
 * @ingroup flag_line
 *
 * @ContentEntityType(
 *   id = "run",
 *   label = @Translation("Run"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\flag_line\RunListBuilder",
 *     "views_data" = "Drupal\flag_line\Entity\RunViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\flag_line\Entity\Form\RunForm",
 *       "add" = "Drupal\flag_line\Entity\Form\RunForm",
 *       "edit" = "Drupal\flag_line\Entity\Form\RunForm",
 *       "delete" = "Drupal\flag_line\Entity\Form\RunDeleteForm",
 *     },
 *     "access" = "Drupal\flag_line\RunAccessControlHandler",
 *   },
 *   base_table = "run",
 *   admin_permission = "administer Run entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/run/{run}",
 *     "edit-form" = "/admin/run/{run}/edit",
 *     "delete-form" = "/admin/run/{run}/delete"
 *   },
 *   field_ui_base_route = "run.settings"
 * )
 */
class Run extends ContentEntityBase implements RunInterface {

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
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
  public function getUpdatePeriod() {
    return $this->get('update_period')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumStations() {
    return (int) $this->get('num_stations')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengers() {
    return (int) $this->get('num_passengers')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getTrainStatus() {
    return $this->get('train_status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getStationsStatus() {
    return $this->get('stations_status')->value;
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
  public function setTrainStatus($status) {
    switch ($status) {
      case RunInterface::TRAINS_NOT_YET_RUN:

      case RunInterface::TRAINS_RUNNING:

      case RunInterface::TRAINS_STOPPED:

        $this->set('train_status', $status);
        break;

      default:
        // Ignoring $status.
        break;
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setStationsStatus($status) {
    switch ($status) {
      case RunInterface::STATIONS_NOT_YET_OPENED:

      case RunInterface::STATIONS_OPEN:

      case RunInterface::STATIONS_CLOSED:

        $this->set('stations_status', $status);
        break;

      default:
        // Ignore $status.
        break;
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Run entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Run entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Run entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Run entity.'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['update_period'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Update Period'))
      ->setDescription(t('The period in seconds over which events are define.'))
      ->setRequired(TRUE)
      ->setDefaultValue(5)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['num_passengers'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Passengers'))
      ->setDescription(t('The number of passengers generated in the update period.'))
      ->setRequired(TRUE)
      ->setDefaultValue(50)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['num_stations'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Stations'))
      ->setDescription(t('The number of stations.'))
      ->setRequired(TRUE)
      ->setDefaultValue(10)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['train_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Trains'))
      ->setDescription(t('One of the TRAIN constants indicating wheather the service has run.'))
      ->setRequired(TRUE)
      ->setDefaultValue(RunInterface::TRAINS_NOT_YET_RUN)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['stations_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Stations'))
      ->setDescription(t('One of the STATIONS constants indicating the state of the stations.'))
      ->setRequired(TRUE)
      ->setDefaultValue(RunInterface::STATIONS_NOT_YET_OPENED)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Run entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}

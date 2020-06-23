<?php

namespace Drupal\flag_line\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Run Entity entity.
 *
 * @ingroup flag_line
 *
 * @ContentEntityType(
 *   id = "run_entity",
 *   label = @Translation("Run Entity"),
 *   handlers = {
 *     "storage" = "Drupal\flag_line\RunEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\flag_line\RunEntityListBuilder",
 *     "views_data" = "Drupal\flag_line\Entity\RunEntityViewsData",
 *     "translation" = "Drupal\flag_line\RunEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\flag_line\Form\RunEntityForm",
 *       "add" = "Drupal\flag_line\Form\RunEntityForm",
 *       "edit" = "Drupal\flag_line\Form\RunEntityForm",
 *       "delete" = "Drupal\flag_line\Form\RunEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\flag_line\RunEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\flag_line\RunEntityAccessControlHandler",
 *   },
 *   base_table = "run_entity",
 *   data_table = "run_entity_field_data",
 *   revision_table = "run_entity_revision",
 *   revision_data_table = "run_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer run entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/run_entity/{run_entity}",
 *     "add-form" = "/admin/structure/run_entity/add",
 *     "edit-form" = "/admin/structure/run_entity/{run_entity}/edit",
 *     "delete-form" = "/admin/structure/run_entity/{run_entity}/delete",
 *     "version-history" = "/admin/structure/run_entity/{run_entity}/revisions",
 *     "revision" = "/admin/structure/run_entity/{run_entity}/revisions/{run_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/run_entity/{run_entity}/revisions/{run_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/run_entity/{run_entity}/revisions/{run_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/run_entity/{run_entity}/revisions/{run_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/run_entity",
 *   },
 *   field_ui_base_route = "run_entity.settings"
 * )
 */
class RunEntity extends EditorialContentEntityBase implements RunEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

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
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the run_entity owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
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
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
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
  public function getUpdatePeriod() : int {
    return $this->get('update_period')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumStations() : int {
    return (int) $this->get('num_stations')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengers() : int {
    return (int) $this->get('num_passengers')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getTrainStatus() : string {
    return $this->get('train_status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getStationsStatus() : string {
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
  public function setTrainStatus(string $status) {
    switch ($status) {
      case RunEntityInterface::TRAINS_NOT_YET_RUN:

      case RunEntityInterface::TRAINS_RUNNING:

      case RunEntityInterface::TRAINS_STOPPED:

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
  public function setStationsStatus(string $status) {
    switch ($status) {
      case RunEntityInterface::STATIONS_NOT_YET_OPENED:

      case RunEntityInterface::STATIONS_OPEN:

      case RunEntityInterface::STATIONS_CLOSED:

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
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Run Entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
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
      ->setDescription(t('The name of the Run Entity entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

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
      ->setDefaultValue(RunEntityInterface::TRAINS_NOT_YET_RUN)
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
      ->setDefaultValue(RunEntityInterface::STATIONS_NOT_YET_OPENED)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Run Entity is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}

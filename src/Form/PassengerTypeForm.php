<?php

namespace Drupal\flag_line\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PassengerTypeForm.
 *
 * @package Drupal\flag_line\Form
 */
class PassengerTypeForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $passenger_type = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $passenger_type->label(),
      '#description' => $this->t("Label for the Passenger type."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $passenger_type->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\flag_line\Entity\PassengerType::load',
      ),
      '#disabled' => !$passenger_type->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $passenger_type = $this->entity;
    $status = $passenger_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Passenger type.', [
          '%label' => $passenger_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Passenger type.', [
          '%label' => $passenger_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($passenger_type->urlInfo('collection'));
  }

}

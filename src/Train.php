<?php

/**
 * @file
 * Contains Drupal\flag_line\Train.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\TrainInterface;
use Drupal\flag_line\PassengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
/**
 * An implementation of the TrainInterface.
 *
 * Contains simple passenger accessor methods.
 *
 * @package Drupal\flag_line
 */
class Train implements TrainInterface {
  use StringTranslationTrait;

  /**
   * An collect of lists storing passengers on the train.
   *
   * Passengers are store on a list indexed by destination stations.
   */
  private $passengerLists = [];

  /**
   * Construct a train from a list of stations it is to visit.
   *
   * @param array $station_names
   *   An array of station ids.
   */
  public function __construct(array $station_names) {
    // Initialize passenger lists - one for each station.
    foreach ($station_names as $name) {
      $this->passengerLists[] = [];
    };
  }

  /**
   * {@inheritdoc}
   */
  public function addPassenger(PassengerInterface $passenger) {
    $passenger
      ->setBoarded()
      ->save();
    $station_id = $passenger->getDst();
    $this->passengerLists[$station_id][] = $passenger;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengers() {
    $count = 0;
    foreach ($this->passengerLists as $list) {
      $count += count($list);
    }
    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceName() {
    $name = $this->serviceName;
    if (!$name) {
      $name = 'Not in Service';
    }
    return $name;
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
  public function setServiceName($name) {
    $this->serviceName = $name;
  }

}

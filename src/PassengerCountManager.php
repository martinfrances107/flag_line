<?php

/**
 * @file
 * Contains Drupal\flag_line\PassengerCountManager.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\Query\QueryFactory;

/**
 * An instance of the passenger count manager interface.
 *
 * @package Drupal\flag_line
 */
class PassengerCountManager implements PassengerCountManagerInterface {

  /**
   * Object use to interogate the passenger store.
   *
   * @var Drupal\Core\Entity\Query\QueryInterface
   */
  private $passengerQuery;

  /**
   * The constructor.
   *
   * @param Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   A query factory use to interogtate passengers.
   */
  public function __construct(QueryFactory $query_factory) {
    $this->passengerQuery = $query_factory->get('passenger');
  }

  /**
   * {@inheritdoc}
   */
  public function getNumTicketsIssued($run_id) {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('run_id', $run_id)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumJourneysComplete($run_id) {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('run_id', $run_id)
      ->condition('alighted', 1, '=')
      ->condition('boarded', 1, '=')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengersOnTrains($run_id) {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('run_id', $run_id)
      ->condition('alighted', 0, '=')
      ->condition('boarded', 1, '=')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengersOnPlatforms($run_id) {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('run_id', $run_id)
      ->condition('alighted', 0, '=')
      ->condition('boarded', 0, '=')
      ->count()
      ->execute();

  }

}

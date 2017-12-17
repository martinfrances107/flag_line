<?php

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
    $this->passengerQuery = $query_factory->get('node');
    $this->passengerQuery->condition('type', 'passenger');
  }

  /**
   * {@inheritdoc}
   */
  public function getNumTicketsIssued(Integer $run_id) : Integer {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('field_run_id', $run_id)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumJourneysComplete(Integer $run_id) : Integer {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('field_run_id', $run_id)
      ->condition('field_alighted', 1, '=')
      ->condition('field_boarded', 1, '=')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengersOnTrains(Integer $run_id) : Integer {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('field_run_id', $run_id)
      ->condition('field_alighted', 0, '=')
      ->condition('field_boarded', 1, '=')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getNumPassengersOnPlatforms(Integer $run_id) : Integer {
    $query = clone $this->passengerQuery;

    return $query
      ->condition('field_run_id', $run_id)
      ->condition('field_alighted', 0, '=')
      ->condition('field_boarded', 0, '=')
      ->count()
      ->execute();

  }

}

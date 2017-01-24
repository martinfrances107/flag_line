<?php

namespace Drupal\flag_line;

/**
 * Provides an service definition for counting passingers.
 *
 * @ingroup flag_line
 */
interface PassengerCountManagerInterface {

  /**
   * Returns the number of passenger created.
   *
   * @param int $run_id
   *   The run id.
   *
   * @return int
   *   The number counted.
   */
  public function getNumTicketsIssued($run_id);

  /**
   * Returns the number of passenger moved from platform to train to exit.
   *
   * @param int $run_id
   *   The run id.
   *
   * @return int
   *   The number counted.
   */
  public function getNumJourneysComplete($run_id);

  /**
   * Returns the number of passengers moved from platform on a train.
   *
   * @param int $run_id
   *   The run id.
   *
   * @return int
   *   The number counted.
   */
  public function getNumPassengersOnTrains($run_id);

  /**
   * The number created and placed on a platform.
   *
   * @param int $run_id
   *   The run id.
   *
   * @return int
   *   The number counted.
   */
  public function getNumPassengersOnPlatforms($run_id);

}

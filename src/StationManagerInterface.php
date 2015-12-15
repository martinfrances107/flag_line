<?php

/**
 * @file
 * Contains Drupal\flag_line\StationManagerInterface.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\RunInterface;

/**
 * Methods to list and control stations on the line.
 *
 * @package Drupal\flag_line
 */
interface StationManagerInterface {

  /**
   * Returns a list of platforms for a given direction of travel.
   *
   * @param int $run_id
   *   The run identifer.
   * @param int $num_stations
   *   The number of stations on the line.
   * @param bool $upwards
   *   Is the direciton of travel up the line?
   *
   * @return \Drupal\flag_line\PlatformInterface[]
   *   An ordered list of platforms.
   */
  public function getPlatforms($run_id, $num_stations, $upwards);

  /**
   * Populates stations with new passengers.
   *
   * For tracking new passenger must be associated with a particular run.
   *
   * @param int $num_passengers
   *   The number of passenger to add to stations.
   * @param \Drupal\flag_line\RunInterface $run
   *   The run identifier.
   */
  public function populateStationsAtRandom($num_passengers, RunInterface $run);

}

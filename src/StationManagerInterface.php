<?php

namespace Drupal\flag_line;

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
   *   The run identifier.
   * @param int $num_stations
   *   The number of stations on the line.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   *
   * @return \Drupal\flag_line\PlatformInterface[]
   *   An ordered list of platforms.
   */
  public function getPlatforms(int $run_id, int $num_stations, bool $upwards) : array;

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
  public function populateStationsAtRandom(int $num_passengers, RunInterface $run);

}

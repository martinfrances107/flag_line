<?php

/**
 * @file
 * Contains Drupal\flag_line\StationManagerInterface.
 */

namespace Drupal\flag_line;

/**
 * Methods to list and control stations on the line.
 *
 * @package Drupal\flag_line
 */
interface StationManagerInterface {

  /**
   * Returns the number of stations.
   *
   * @return int
   *   The number of stations.
   */
  public function getNumStations();

  /**
   * Returns a list of platforms for a given direction of travel.
   *
   * @param bool $upwards
   *   Is the direciton of travel up the line?
   *
   * @return \Drupal\flag_line\PlatformInterface[]
   *   An ordered list of platforms.
   */
  public function getPlatforms($upwards);

  /**
   * Returns a list of stations for a given direction of travel.
   *
   * @param bool $upwards
   *   Is the direction of travel up the line? Default: TRUE.
   *
   * @return array
   *   An array of station ids.
   */
  public function getStationNames($upwards = TRUE);

  /**
   * Populates stations with new passengers.
   *
   * For tracking new passenger must be associated with a particular run.
   *
   * @param int $num_passengers
   *   The number of passenger to add to stations.
   * @param int $run_id
   *   The run identifier.
   */
  public function populateStationsAtRandom($num_passengers, $run_id);

  /**
   * Set the number of stations on the line.
   *
   * @param int $num_stations
   *   The number of stations.
   *
   * @return static
   *   The object itself for chaining.
   */
  public function setNumStations($num_stations);

}

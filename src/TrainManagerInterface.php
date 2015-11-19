<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainManagerInterface.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\TrainEntityInterface;

/**
 * Handle the operation of trains up and down the line.
 *
 * @package Drupal\flag_line
 */
interface TrainManagerInterface {

  /**
   * Run a train through an ordered listed of station platforms to visit.
   *
   * At each station:
   *
   * - Unload passenegrs.
   * - Load passenegrs.
   *
   * At the terminus, perform validation. Ensure no passenger remain.
   *
   * @param Drupal\flag_line\TrainEntityInterface $train
   *   A instance of a train.
   * @param \Drupal\flag_line\PlatformInterface[] $platforms
   *   A ordered list of platforms.
   *
   * @return bool
   *   FLASE when there were problems encountered.
   */
  public function runService(TrainEntityInterface $train, array $platforms);

  /**
   * Returns a list of passenger leave the train at the given station.
   *
   * @param Drupal\flag_line\TrainEntityInterface $train
   *   A instance of a train.
   * @param int $station_id
   *   A station identifier.
   */
  public function getDepartingPassengers(TrainEntityInterface $train, $station_id);

}

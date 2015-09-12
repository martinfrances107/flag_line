<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainManagerInterface.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\TrainInterface;

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
   * @param string $name
   *   The name of the service.
   * @param \Drupal\flag_line\TrainInterface $train
   *   A instance of a train.
   * @param \Drupal\flag_line\PlatformInterface[] $platforms
   *   A ordered list of platforms.
   *
   * @return bool
   *   FLASE when there were problems encountered.
   */
  public function runService($name, TrainInterface $train, array $platforms);

}

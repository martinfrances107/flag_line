<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainManager.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\TrainInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\flag_line\PlatformInterface;
use Drupal\flag_line\PassengerInterface;

/**
 * An instance of the train manager interface.
 *
 * @package Drupal\flag_line
 */
class TrainManager implements TrainManagerInterface {

  /**
   * Records trains activity.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function runService($name, TrainInterface $train, array $platforms) {
    // Keep counts.
    $num_loaded = 0;
    $num_unloaded = 0;

    // Signal train in service.
    $train->setServiceName($name);
    $this->logger->notice("$name: - starts.");

    // Run alog the line.
    foreach ($platforms as $platform) {
      $num_loaded += $this->loadPassengers($platform, $train);
      $num_unloaded += $this->unloadPassengers($train, $platform);
    }

    // Final checks - the train should be empty.
    $test_result = ($num_loaded === $num_unloaded);

    // Log test result.
    $info = "loaded: $num_loaded - unloaded:$num_unloaded.";
    if ($test_result) {
      $this->logger->notice("$name: Complete - $info");
    }
    else {
      $this->logger->error("$name: Passenger mismatch! - $info");
    }

    $num_onboard = $train->getNumPassengers();
    if ($num_onboard != 0) {
      $this->logger->error("name: $num_onboard Passenger(s) incorrectly found on train at terminus.");
      $test_result = FALSE;
    }
    else {
      $this->logger->notice("$name: Train is empty at terminus.");
    }

    return $test_result;
  }

  /**
   * Transfer passengers from the platform to the train.
   *
   * @param \Drupal\flag_line\PlatformInterface $platform
   *   The platform queue to empty.
   * @param TrainInterface $train
   *   The train to load.
   * @param int $wait
   *   The time in seconds to spend loading the train.
   *
   * @return int
   *   The number of passegers loaded.
   */
  private function loadPassengers(PlatformInterface $platform, TrainInterface $train, $wait = 5) {

    $num_passengers = 0;
    $sn = $train->getServiceName();
    /* @var $queue \Drupal\Core\QueueInterface */
    $queue = $platform->getQueue();

    // For a limited time, transfer passgeners from the platform to train.
    $end = time() + $wait;
    while (time() < $end && ($item = $queue->claimItem())) {
      try {
        $passenger = $item->data;
        if ($passenger instanceof PassengerInterface) {
          // Checks.
          if ($passenger->hasAlighted()) {
            $this->logger->error("$sn: Loading a passenger who has already gotten off a train.");
          }
          if ($passenger->hasBoarded()) {
            $this->logger->error("$sn: Loading a passenger who has already boarded a train.");
          }

          $pid = $passenger->id();
          // $this->logger->debug("$sn: loading passenger with id $pid to train.");
          $train->addPassenger($passenger);
          $queue->deleteItem($item);
          // Only consider the passenger added, when he/she is no longer on the
          // platform queue.
          $num_passengers++;
        }
      }
      catch (SuspendQueueException $e) {
        // If the worker indicates there is a problem with the whole queue,
        // release the item.
        $this->logger->error("$sn: Leaving passenger at the station!");
        $queue->releaseItem($item);
      }
      catch (\Exception $e) {
        // In case of any other kind of exception, leave the item
        // in the queue to be processed again later.
        $this->logger->emergency("$sn: Unexpected exception loading passenegrs.");
      }
    }

    // Debug.
    // $station_id = $platform->getStationId();
    // $this->logger->notice("$sn: $num_passengers passenger(s) loaded at station $station_id.");

    return $num_passengers;
  }

  /**
   * Transfer the passengers from train onto platform.
   *
   * @param TrainInterface $train
   *   The train unloading passengers.
   * @param PlatformInterface $platform
   *   The platform receiving the passengers.
   *
   * @return int
   *   The number of passengers unloaded.
   */
  private function unloadPassengers(TrainInterface $train, PlatformInterface $platform) {
    $sn = $train->getServiceName();
    $station_id = $platform->getStationId();
    $passengers = $train->removePassengers($station_id);
    $num_passengers = count($passengers);
    // $this->logger->info("$sn: Unloading $num_passengers passenger(s) at $station_id");

    // Process departing passenger.
    foreach ($passengers as $passenger) {
      // $pid = $passenger->id();
      // $this->logger->debug("$sn: Unloading passenger $pid from train");
      if (!($passenger instanceof PassengerInterface)) {
        // Checks.
        $name = get_class($passenger);
        if (is_object($passenger)) {
          $name = get_class($passenger);
        }
        else {
          $name = gettype($passenger);
        }
        $this->logger->error("$sn: Did not pull a passenger off the train! - class/type $name.");
      }

      if (!$passenger->hasBoarded()) {
        $pid = $passenger->id();
        $this->logger->error("$sn: Unloading at $station_id - passenger $pid - Did NOT boarded a train.");
      }

      if ($passenger->hasAlighted()) {
        $pid = $passenger->id();
        $this->logger->error("$sn: Unloading at $station_id - passenger $pid - Cannot get off the train more than once.");
      }

      $passenger_dst = $passenger->getDst();
      if ($passenger_dst != $station_id) {
        $pid = $passenger->id();
        $this->logger->error("$sn: Unloading at $station_id - passenger $pid - Did not alight at station $passenger_dst.");
      }

      // Unflag the passenger as they leave the station.
      $passenger
        ->setAlighted()
        ->save();

      // @TODO: Maybe delete passenger.
    }

    return $num_passengers;
  }

}

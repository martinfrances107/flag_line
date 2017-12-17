<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;

/**
 * An instance of the train manager interface.
 *
 * @package Drupal\flag_line
 */
class TrainManager implements TrainManagerInterface {

  /**
   * Object use to interogate the passenger store.
   *
   * @var Drupal\Core\Entity\Query\QueryInterface
   */
  private $passengerQuery;

  /**
   * Records trains activity.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The constructor.
   *
   * @param Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   A query factory use to interogtate passengers.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(QueryFactory $query_factory, LoggerInterface $logger) {
    $this->passengerQuery = $query_factory->get('node');
    $this->passengerQuery
      ->condition('type', 'passenger')
      ->accessCheck(FALSE);
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function runService(NodeInterface $train, array $platforms) {
    // Keep counts.
    $num_loaded = 0;
    $num_unloaded = 0;

    $sn = $train->title->value;
    $this->logger->notice("$sn: - starts.");

    // Run alog the line.
    foreach ($platforms as $platform) {
      $num_loaded += $this->loadPassengers($platform, $train);
      $num_unloaded += $this->unloadPassengers($train, $platform);
    }

    // Final checks - the train should be empty.
    $test_result = ($num_loaded === $num_unloaded);

    // Log test result.
    $info = "loaded: $num_loaded - unloaded: $num_unloaded.";
    if ($test_result) {
      $this->logger->notice("$sn: Complete - $info");
    }
    else {
      $this->logger->error("$sn: Passenger mismatch. - $info");
    }

    return $test_result;
  }

  /**
   * Transfer passengers from the platform to the train.
   *
   * @param Drupal\flag_line\PlatformInterface $platform
   *   The platform queue to empty.
   * @param \Drupal\node\NodeInterface $train
   *   The train to load.
   * @param int $wait
   *   The time in seconds to spend loading the train.
   *
   * @return int
   *   The number of passegers loaded.
   */
  private function loadPassengers(PlatformInterface $platform, NodeInterface $train, $wait = 5) : int {

    $num_passengers = 0;
    $sn = $train->title->value;
    /* @var $queue \Drupal\Core\QueueInterface */
    $queue = $platform->getQueue();

    // For a limited time, transfer passgeners from the platform to train.
    $end = time() + $wait;
    while (time() < $end && ($item = $queue->claimItem())) {
      try {
        /** @var Drupal\node\NodeInterface $passenger */
        $passenger = $item->data;

        if ($passenger instanceof NodeInterface && ($passenger->getType() == 'passenger')) {
          // Checks.
          if ($passenger->field_alighted->value) {
            $this->logger->error("$sn: Loading a passenger who has already gotten off a train.");
          }
          if ($passenger->field_boarded->value) {
            $this->logger->error("$sn: Loading a passenger who has already boarded a train.");
          }

          $pid = $passenger->id();
          $this->logger->debug("$sn: Loading passenger with id $pid to train.");

          // Mark passenger as being on the train.
          $passenger->field_boarded->value = 1;
          $passenger->field_train = $train->id();
          $passenger->save();

          // Add passenger to the train.
          $train->field_passenger[] = $passenger->id();

          $queue->deleteItem($item);
          // Only consider the passenger added, when he/she is no longer on the
          // platform queue.
          $num_passengers++;
        }
        else {
          $this->logger->error("$sn: Trrain manager - pulled unexpected item offf queue");
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
        $this->logger->error("$sn: Unexpected exception loading passengers.");
      }
    }

    // Loading complete.
    $train->save();

    // Debug.
    $station_id = $platform->getStationId();
    $this->logger->debug("$sn: $num_passengers passenger(s) loaded at station $station_id.");

    return $num_passengers;
  }

  /**
   * Transfer the passengers from train onto platform.
   *
   * @param \Drupal\node\NodeInterface $train
   *   The train unloading passengers.
   * @param Drupal\flag_line\PlatformInterface $platform
   *   The platform receiving the passengers.
   *
   * @return int
   *   The number of passengers unloaded.
   */
  private function unloadPassengers(NodeInterface $train, PlatformInterface $platform) : int {
    $sn = $train->title->value;
    $station_id = $platform->getStationId();
    $passengers = $this->getDepartingPassengers($train->id(), $station_id);
    $num_passengers = count($passengers);
    $this->logger->debug("$sn: Unloading $num_passengers passenger(s) at $station_id");

    // Process departing passenger.
    foreach ($passengers as $passenger) {
      $pid = $passenger->id();
      $this->logger->debug("$sn: Unloading passenger $pid from train");

      // Unflag the passenger as they leave the station.
      $passenger
        ->set('field_alighted', 1)
        ->save();

    }

    return $num_passengers;
  }

  /**
   * Gets passengers leaving a train at a given station.
   *
   * @param int $train_id
   *   The Train indentifier.
   * @param int $station_id
   *   The station identifier.
   *
   * @return Drupal\flag_line\PassengerInterface[]
   *   The passengers.
   */
  private function getDepartingPassengers($train_id, $station_id) : array {
    $query = clone $this->passengerQuery;

    $passenger_ids = $query
      ->condition('field_dst', $station_id, '=')
      ->condition('field_alighted', 0, '=')
      ->condition('field_boarded', 1, '=')
      ->condition('field_train', $train_id, '=')
      ->execute();

    $passengers = Node::loadMultiple($passenger_ids);

    // Return departing passengers.
    return $passengers;
  }

}

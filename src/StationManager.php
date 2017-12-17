<?php

namespace Drupal\flag_line;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Psr\Log\LoggerInterface;

/**
 * An instance of the station manager interface.
 *
 * @package Drupal\flag_line
 */
class StationManager implements StationManagerInterface {

  /**
   * A list of platforms to visit in assending order.
   *
   * @var \Drupal\flag_line\PlatformInterface[]|null
   */
  protected $platformsUp = NULL;

  /**
   * A list of platforms to visit in descending order.
   *
   * @var \Drupal\flag_line\PlatformInterface[]|null
   */
  protected $platformsDown = NULL;

  /**
   * The number of stations on the line.
   *
   * @var int
   */
  protected $numStations;

  /**
   * A factory for generating platform queues.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Keeps records of passenger movements etc.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructor.
   *
   * @param Drupal\Core\Queue\QueueFactory $queue_factory
   *   A queue factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(QueueFactory $queue_factory, LoggerInterface $logger) {
    $this->queueFactory = $queue_factory;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlatforms(int $run_id, int $num_stations, bool $upwards) : array {
    // Return a cached list where possible, generate when needed.
    if ($upwards) {
      if (is_null($this->platformsUp)) {
        $this->platformsUp = $this->generatePlatforms($run_id, $num_stations, TRUE);
      }
      return $this->platformsUp;
    }
    else {
      if (is_null($this->platformsDown)) {
        $this->platformsDown = $this->generatePlatforms($run_id, $num_stations, FALSE);
      }
      return $this->platformsDown;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function populateStationsAtRandom(int $num_passengers, RunInterface $run) {
    $run_id = $run->id();
    for ($i = 0; $i < $num_passengers; $i++) {
      $passenger = $this->generatePassengerAtRandom($run);
      $this->addPassengerToPlatform($passenger);
    }
    $this->logger->debug("Run:$run_id: $num_passengers passengers arrive at platforms.");
  }

  /**
   * Generate passenger.
   *
   * Generate a random ticket and passenger type based on information extracted
   * from the current run.
   *
   * @param \Drupal\flag_line\RunInterface $run
   *   The run identifier.
   *
   * @return \Drupal\flag_line\Entity\Passenger
   *   A new passenger.
   */
  private function generatePassengerAtRandom(RunInterface $run) : Passenger {
    // Generate ticket information.
    $max = $run->getNumStations() - 1;
    $src = rand(1, $max);
    $dst = rand(1, $max);

    $run_id = $run->id();
    $passenger = Node::create([
      'type' => 'passenger',
      'title' => "Passenger on Run: $run_id src $src dst $dst",
      'field_run_id' => $run_id,
      'field_src' => $src,
      'field_dst' => $dst,
      'field_upwards' => ($src <= $dst) ? 1 : 0,
    ]);
    $passenger->save();

    return $passenger;
  }

  /**
   * Place passenger in the appropiate platform queue.
   *
   * If the src and dst are equal the passenger will still be placed in a
   * platform queue it is up to the train to unload correctly.
   *
   * @param \Drupal\node\NodeInterface $passenger
   *   An instance of a passenger.
   */
  private function addPassengerToPlatform(NodeInterface $passenger) {
    // Must check platforms exists before putting passenger into the system.
    $run_id = $passenger->field_run_id->target_id;
    $upwards = $passenger->field_upwards->value;
    $station_id = $passenger->field_src->value;

    // Add passenger to the correct queue.
    $platform_queue = $this->getPlatformQueue($run_id, $station_id, $upwards);
    $platform_queue->createItem($passenger);
  }

  /**
   * Returns a platform queue for a station and direction of travel.
   *
   * Generates a list of platform queues when first called and then caches them.
   *
   * @param int $run_id
   *   The run identifier.
   * @param int $station_id
   *   A station identifier.
   * @param bool $upwards
   *   TRUE if the direction of travel is upwards.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The platform queue.
   */
  private function getPlatformQueue(int $run_id, int $station_id, bool $upwards) : QueueInterface {
    // Identify the platform.
    $name = $this->getPlatformName($run_id, $station_id, $upwards);
    $platform = $this->queueFactory->get($name, TRUE);

    // TODO try catch - throw error if platform cannot be found.
    return $platform;
  }

  /**
   * Returns a platform name for a given station and direction of travel.
   *
   * This method is intended to be the sole line specfic mapping.
   * It is used by both trains and passegers to identify the approiate
   * platform for a given direction of travel.
   *
   * @param int $run_id
   *   The run identifier.
   * @param int $station_id
   *   The station identifier.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   *
   * @return string
   *   The platform name.
   */
  private function getPlatformName(int $run_id, int $station_id, bool $upwards) : string {
    $this->logger->notice("getting name run:$run_id");
    if ($upwards) {
      $name = "R:$run_id-S:$station_id-P:A";
    }
    else {
      $name = "R:$run_id-S:$station_id-P:B";
    }

    return $name;
  }

  /**
   * Returns a list of stations for a given direction of travel.
   *
   * @param int $num_stations
   *   Number of stations.
   * @param bool $upwards
   *   TRUE if the direction of travel up the line?
   *
   * @return array
   *   An array of station identifiers.
   */
  private function getStationIds(int $num_stations, bool $upwards) : array {
    if ($upwards) {
      $range = range(0, $num_stations - 1, 1);
    }
    else {
      $range = range($num_stations - 1, 0, -1);
    }

    return $range;
  }

  /**
   * Generate platforms queues for a given direction of travel.
   *
   * @param int $run_id
   *   The run identifier.
   * @param int $num_stations
   *   The number of stations on the line.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   *
   * @return \Drupal\flag_line\PlatformInterface[]
   *   An ordered list of platforms who order is based on the direction of
   *   travel.
   */
  private function generatePlatforms(int $run_id, int $num_stations, bool $upwards) : array {
    $platforms = [];

    foreach ($this->getStationIds($num_stations, $upwards) as $station_id) {
      $platforms[] = $this->generatePlatform($run_id, $station_id, $upwards);
    }
    return $platforms;
  }

  /**
   * Generate a new platform for a given direction of travel.
   *
   * @param int $run_id
   *   The run identifier.
   * @param int $station_id
   *   The station identifier.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   */
  private function generatePlatform(int $run_id, int $station_id, bool $upwards) {
    $this->logger->notice("Generating platform run:$run_id");
    $platform_name = $this->getPlatformName($run_id, $station_id, $upwards);
    $queue = $this->queueFactory->get($platform_name);
    $queue->createQueue();
    return new Platform($platform_name, $station_id, $queue);
  }

}

<?php

/**
 * @file
 * Contains Drupal\flag_line\Controller\ScoreboardController.
 */

namespace Drupal\flag_line\Controller;

use Drupal\Core\Controller\ControllerBase;
USE Drupal\flag_line\RunInterface;
use Drupal\flag_line\PassengerCountManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

/**
 * Class ScoreboardController.
 *
 * @package Drupal\flag_line\Controller
 */
class ScoreboardController extends ControllerBase {

  /**
   * The count manager.
   *
   * @var \Drupal\flag_line\PassengerCountManagerInterface
   */
  protected $count;

  /**
   * {@inheritdoc}
   */
  public function __construct(PassengerCountManagerInterface $passenger_count) {
    $this->count = $passenger_count;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flag_line.passenger_count_manager')
    );
  }

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index(RunInterface $run) {

    $tm = \Drupal::service('flag_line.train_manager');

    $query_factory = \Drupal::service('entity.query');
    $query = $query_factory->get('node');

    $train = Node::create(['type' => 'train', 'title' => 'test train2']);
    $train->save();

    $passenger = Node::create([
      'type' => 'passenger',
      'title' => 'no name2',
      'field_dst'=> 5,
      'field_boarded' => 1,
      'field_alighted' => 0,
      'field_train' => $train->id(),
      ]);
    $passenger->save();

    /// Sample bind passenger to train;
    //$train->field_passenger[] =  $passenger->id();
    //$train->save();
var_dump($passenger->field_dst->value);
    var_dump($tm->removeDepartingPassengers($train->id(), 5));

    //var_dump($passenger->field_train->value);
var_dump($train->id());
    /*
    $passenger_ids = $query
      ->condition('type', 'passenger', '=')
      ->condition('field_dst', 1, '=')
      ->condition('field_alighted', '0', '=')
      ->condition('field_boarded', '1', '=')
      ->condition('field_train', $train->id(), '=')
      ->execute();
    */
    $passenger_ids = $query
      ->condition('type', 'train', '=')
      ->condition('field_passenger.entity.field_src', 1234, '=')
      ->execute();
    $passengers = Node::loadMultiple($passenger_ids);
var_dump($passengers);


$run_id = 1;
    //$run_id = $run->id();
    //var_dump($this->count->getNumTicketsIssued($run_id));
    return [
      '#theme' => 'scoreboard',
      //'#numTicketsIssued' => $this->count->getNumTicketsIssued($run_id),
      //'#numJourneysComplete' => $this->count->getNumJourneysComplete($run_id),
      //'#numPassengersOnPlatforms' => $this->count->getNumPassengersOnPlatforms($run_id),
      //'#numPassengersOnTrains' => $this->count->getNumPassengersOnTrains($run_id),
    ];
  }

}

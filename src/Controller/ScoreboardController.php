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
   * @return []
   *   Render from scoreboard theme.
   */
  public function index(RunInterface $run) {

    $run_id = $run->id();
    return [
      '#theme' => 'scoreboard',
      '#numTicketsIssued' => $this->count->getNumTicketsIssued($run_id),
      '#numJourneysComplete' => $this->count->getNumJourneysComplete($run_id),
      '#numPassengersOnPlatforms' => $this->count->getNumPassengersOnPlatforms($run_id),
      '#numPassengersOnTrains' => $this->count->getNumPassengersOnTrains($run_id),
    ];
  }

}

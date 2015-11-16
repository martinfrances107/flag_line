<?php

/**
 * @file
 * Contains Drupal\flag_line\Command\OpenStationsCommand.
 */

namespace Drupal\flag_line\Command;

use Drupal\flag_line\Entity\Run;
use Drupal\flag_line\RunInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Command\ContainerAwareCommand;

/**
 * Class OpenStationsCommand.
 *
 * @package Drupal\flag_line
 */
class OpenStationsCommand extends ContainerAwareCommand {

  /**
   * Source and respoitory of information about the run.
   *
   * @var Drupal\flag_line\RunInterface
   */
  private $run;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('flag_line:openStations')
      ->setDescription($this->trans('command.flag_line.openStations.description'))
      ->addArgument(
        'run_id', InputArgument::REQUIRED, $this->trans('command.flag_line.openStations.arguments.run_id')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {

    $output->writeln('Station process initializing');
    parent::initialize($input, $output);

    //$run_storage = $this->getContainer()->get('entity.query')->getStorage('Run');

    // Validate the input paramters.
    $run_id = $input->getArgument('run_id');

    // Bodge .... must find a better way to solve entity caching!
    sleep(4);

    $run = Run::load($run_id);
    // Validate run.
    if (is_null($run)) {
      throw new \RunTimeException($this->trans('Cannot find that run'));
    }

    $output->writeln('loaded.');

    // Wait a while, until the other process starts.
    $count = 0;
    while ($run->getTrainStatus(TRUE) === RunInterface::TRAINS_NOT_YET_RUN && $count < 4) {
      sleep(1);
      $count++;
      // TODO find a better way to refresh the TrainStatus
      $run->load($run_id);
    }

    $status = $run->getTrainStatus();
    if ($status !== RunInterface::TRAINS_RUNNING) {
      $output->writeln("\nStation process error: trains status = $status - cannot initialize.");
      throw new \RunTimeException($this->trans('Trains must be running.'));
    }

    $this->run = $run;

    $output->writeln('Station process initialized');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Information about the run.
    $wait = 0.5 * $this->run->getUpdatePeriod();
    $num_passengers = $this->run->getNumPassengers();
    $num_stations = $this->run->getNumStations();

    /* @var $station_manager \Drupal\flag_line\StationManagerInterface */
    $station_manager = $this->getContainer()->get('flag_line.station_manager');
    $station_manager->setNumStations($num_stations);

    // Open stations to passengers.
    $this->run
      ->setStationsStatus(RunInterface::STATIONS_OPEN)
      ->save();

    $run_id = $this->run->id();
    // Continuous operations.
    try {
      while (TRUE) {
        $station_manager->populateStationsAtRandom($num_passengers, $run_id);
        $output->write('+');
        sleep($wait);
      }
    } catch (Exception $e) {
      $this->run
        ->setStationsStatus(RunInterface::STATIONS_CLOSED)
        ->save();
    }
  }

}

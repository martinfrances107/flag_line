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
        'run_id', InputArgument::REQUIRED, $this->trans('command.flag_line.openStations.arguments.run_id'
        )
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    // Validate the input paramters.
    $run_id = $input->getArgument('run_id');
    $this->run = Run::load($run_id);
    // Validate run.
    if (is_null($this->run)) {
      throw new \RunTimeException($this->trans('Cannot find that run'));
    }

    if ($this->run->getTrainStatus() !== RunInterface::TRAINS_RUNNING) {
      throw new \RunTimeException($this->trans('Trains must be running.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Run:' . $this->run->name->value);

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
    }
    catch (Exception $e) {
      $this->run
        ->setStationsStatus(RunInterface::STATIONS_CLOSED)
        ->save();
    }
  }

}

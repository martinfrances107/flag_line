<?php

/**
 * @file
 * Contains Drupal\flag_line\Command\StartTrainsCommand.
 */

namespace Drupal\flag_line\Command;

use Drupal\flag_line\Entity\Run;
use Drupal\flag_line\RunInterface;
use Drupal\node\Entity\Node;
use Drupal\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StartTrainsCommand.
 *
 * @package Drupal\flag_line
 */
class StartTrainsCommand extends ContainerAwareCommand {

  /**
   * Source and respoitory of information about the run.
   *
   * @var Drupal\flag_line\Entity\Run
   */
  private $run;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('flag_line:startTrains')
      ->setDescription($this->trans('command.flag_line.startTrains.description'))
      ->addArgument(
        'run_id',
        InputArgument::REQUIRED,
        $this->trans('command.flag_line.startTrains.arguments.run_id')
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);
    // Validate the input paramters.
    $run_id = $input->getArgument('run_id');
    $this->run = Run::load($run_id);
    // Validate run.
    if (is_null($this->run)) {
      $output->writeln("Starting trains -  could not find the run. ");
      throw new \Exception($this->trans('Cannot find that run'));
    }

    $status = $this->run->getTrainStatus();
    if ($status !== RunInterface::TRAINS_NOT_YET_RUN) {
      $output->writeln("Train status: $status - cannot continue ");
      throw new \Exception($this->trans('Cannot restart and running or old run.'));
    }

    $output->writeln('Train process initialized');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('train process, Run:' . $this->run->name->value);

    // Information about the run.
    $half_time = 0.5 * $this->run->getUpdatePeriod();
    $num_stations = $this->run->getNumStations();

    // Information about the line.
    /* @var $station_manager \Drupal\flag_line\StationManagerInterface */
    $station_manager = $this->getContainer()->get('flag_line.station_manager');
    $station_manager->setNumStations($num_stations);
    $platforms_up = $station_manager->getPlatforms(TRUE);
    $platforms_down = $station_manager->getPlatforms(FALSE);
    //$station_names = $station_manager->getStationNames();

    /* @var $train_manager \Drupal\flag_line\TrainManagerInterface */
    $train_manager = $this->getContainer()->get('flag_line.train_manager');

    // Just before train services start, update the run.
    $this->run
      ->setTrainStatus(RunInterface::TRAINS_RUNNING)
      ->save();

    $run_id = $this->run->id();
    $i = 0;
    $test_up = TRUE;
    $test_down = TRUE;
    // Continuous operations.
    while ($test_up && $test_down) {
      // Run train up the line.
      $train_up = Node::create(['title' => "Train R$run_id-$i-U", 'type' => 'train']);
      $train_up->save();

      $test_up = $train_manager->runService($train_up, $platforms_up);
      $output->write('u');
      sleep($half_time);

      // Run train down the line.
      $train_down = Node::create(['title' => "Train R$run_id-$i-D", 'type' => 'train']);
      $train_down->save();
      $test_down = $train_manager->runService($train_down, $platforms_down);
      $output->write('d');

      sleep($half_time);
      $i++;
    }

    // Failure.
    $this->run
      ->setTrainStatus(RunInterface::TRAINS_STOPPED)
      ->save();
  }

}

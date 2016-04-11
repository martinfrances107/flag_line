<?php
namespace Drupal\flag_line\Command;

use Drupal\flag_line\Entity\Run;
use Drupal\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class RunLineCommand.
 *
 * @package Drupal\flag_line
 */
class RunLineCommand extends ContainerAwareCommand {
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('flag_line:run')
      ->setDescription($this->trans('command.flag_line.run.description'))
      ->addArgument(
        'name', InputArgument::REQUIRED, $this->trans('command.flag_line.run.arguments.name')
      )
      ->addOption(
        'update_period', NULL, InputOption::VALUE_OPTIONAL, $this->trans('command.flag_line.run.options.update_period'), 5
      )
      ->addOption(
        'num_stations', NULL, InputOption::VALUE_OPTIONAL, $this->trans('command.flag_line.run.options.num_stations'), 10
      )
      ->addOption(
        'num_passengers', NULL, InputOption::VALUE_OPTIONAL, $this->trans('command.flag_line.run.options.num_passengers'), 5
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $values['name'] = $input->getArgument('name');
    $values['update_period'] = $input->getOption('update_period');
    $values['num_passengers'] = $input->getOption('num_passengers');
    $values['num_stations'] = $input->getOption('num_stations');

    /** @var Drupal\flag_line\Entity\Run $run */
    $run = Run::create($values);
    $run->save();

    $name = $run->name->value;
    $id = $run->id();
    $output->writeln("Created run $name ( id = $id )");

    $train_proc = new Process('drupal flag_line:startTrains ' . $id);
    $train_proc->start();

    $station_proc = new Process('drupal flag_line:openStations ' . $id);
    $station_proc->start();

    while ($train_proc->isRunning() && $station_proc->isRunning()) {
      $output->write($train_proc->getIncrementalOutput());
      $output->write($station_proc->getIncrementalOutput());
      $output->write($train_proc->getIncrementalErrorOutput());
      $output->write($station_proc->getIncrementalErrorOutput());
    }

  }

}

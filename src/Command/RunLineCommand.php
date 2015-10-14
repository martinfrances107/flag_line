<?php

/**
 * @file
 * Contains Drupal\flag_line\Command\RunLineCommand.
 */

namespace Drupal\flag_line\Command;

use Drupal\flag_line\Entity\Run;
use Drupal\flag_line\RunInterface;
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
      ->addArgument('name', InputArgument::OPTIONAL, $this->trans('command.flag_line.run.arguments.name'))
      ->addOption('yell', NULL, InputOption::VALUE_NONE, $this->trans('command.flag_line.run.options.yell'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $name = $input->getArgument('name');
    if ($name) {
      $text = 'Hello ' . $name;
    }
    else {
      $text = 'Hello';
    }

    $text = sprintf(
      '%s, %s: %s',
      $text,
      'I am a new generated command for the module',
      $this->getModule()
    );

    if ($input->getOption('yell')) {
      $text = strtoupper($text);
    }

    $output->writeln($text);
  }

}

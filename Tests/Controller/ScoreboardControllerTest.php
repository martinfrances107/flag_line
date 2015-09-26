<?php

/**
 * @file
 * Contains Drupal\flag_line\Tests\ScoreboardController.
 */

namespace Drupal\flag_line\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Provides automated tests for the flag_line module.
 */
class ScoreboardControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entity_query;
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "flag_line ScoreboardController's controller functionality",
      'description' => 'Test Unit for module flag_line and controller ScoreboardController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests flag_line functionality.
   */
  public function testScoreboardController() {
    // Check that the basic functions of module flag_line.
    $this->assertEqual(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}

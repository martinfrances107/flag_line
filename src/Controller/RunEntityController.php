<?php

namespace Drupal\flag_line\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\flag_line\Entity\RunEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RunEntityController.
 *
 *  Returns responses for Run Entity routes.
 */
class RunEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Run Entity revision.
   *
   * @param int $run_entity_revision
   *   The Run Entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($run_entity_revision) {
    $run_entity = $this->entityTypeManager()->getStorage('run_entity')
      ->loadRevision($run_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('run_entity');

    return $view_builder->view($run_entity);
  }

  /**
   * Page title callback for a Run Entity revision.
   *
   * @param int $run_entity_revision
   *   The Run Entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($run_entity_revision) {
    $run_entity = $this->entityTypeManager()->getStorage('run_entity')
      ->loadRevision($run_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $run_entity->label(),
      '%date' => $this->dateFormatter->format($run_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Run Entity.
   *
   * @param \Drupal\flag_line\Entity\RunEntityInterface $run_entity
   *   A Run Entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(RunEntityInterface $run_entity) {
    $account = $this->currentUser();
    $run_entity_storage = $this->entityTypeManager()->getStorage('run_entity');

    $langcode = $run_entity->language()->getId();
    $langname = $run_entity->language()->getName();
    $languages = $run_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $run_entity->label()]) : $this->t('Revisions for %title', ['%title' => $run_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all run entity revisions") || $account->hasPermission('administer run entity entities')));
    $delete_permission = (($account->hasPermission("delete all run entity revisions") || $account->hasPermission('administer run entity entities')));

    $rows = [];

    $vids = $run_entity_storage->revisionIds($run_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\flag_line\Entity\RunEntityInterface $revision */
      $revision = $run_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $run_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.run_entity.revision', [
            'run_entity' => $run_entity->id(),
            'run_entity_revision' => $vid,
          ]));
        }
        else {
          $link = $run_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.run_entity.translation_revert', [
                'run_entity' => $run_entity->id(),
                'run_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.run_entity.revision_revert', [
                'run_entity' => $run_entity->id(),
                'run_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.run_entity.revision_delete', [
                'run_entity' => $run_entity->id(),
                'run_entity_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['run_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}

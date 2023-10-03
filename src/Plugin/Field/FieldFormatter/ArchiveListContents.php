<?php

namespace Drupal\archive_list_contents\Plugin\Field\FieldFormatter;

use Drupal\Core\Archiver\Zip;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Component\Utility\Html;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'archive_list_contents' formatter.
 *
 * @FieldFormatter(
 *   id = "archive_list_contents",
 *   label = @Translation("List archive contents"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class ArchiveListContents extends FileFormatterBase {

  /**
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, $field_definition, array $settings, $label, $view_mode, array $third_party_settings, FileSystemInterface $file_system) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $element['#attached']['library'][] = 'archive_list_contents/archive_list_contents';
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $file) {
      $uri = $file->getFileUri();
      $realpath = $this->fileSystem->realpath($uri);

      $archive = new Zip($realpath);
      $fileList = $archive->listContents();

      // download link
      $element[$delta][] = [
        '#theme' => 'file_link',
        '#file' => $file,
        '#cache' => [
          'tags' => $file->getCacheTags(),
        ],
      ];
      // file size
      $element[$delta][] = [
        '#markup' => $this->t('<i> - ' . $file->getSize() . ' bytes</i><br>'),
      ];

      $element[$delta]['archiveList'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => 'archivelist-readmore',
        ],
      ];
      $element[$delta]['archiveList'][] = [
        '#markup' => $this->t(implode('<br>', $fileList)),
      ];
    }

    return $element;
  }

}

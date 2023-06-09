<?php

namespace Drupal\examples_batch\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\examples_batch\Service\batchManager;

/**
 * Declares Drush commands process.
 */
class batchCommands extends DrushCommands {

  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * batch service.
   *
   * @var \Drupal\examples_batch\Service\batchManager
   */
  private $batchManager;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   * @param \Drupal\examples_batch\Service\batchManager $batchManager
   *   batch service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannelFactory, batchManager $batchManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->batchManager = $batchManager;
  }

  /**
   *
   * @command examples_batch:watcher
   * @aliases examples_batch
   * 
   */
  public function watcher($type) {
    $operations = [];
    $this->loggerChannelFactory->get('examples_batch')->info('Update nodes batch operations start');
    $this->logger()->notice("Batch operations start.");
    $nodes = $this->batchManager->getNodes($type);
    foreach ($nodes as $id) {
      $operations[] = ['\Drupal\examples_batch\Service\batchManager::processItem', [$id, t('Updating node @id', ['@id' => $id])]];
    }
    $batch = [
      'title' => t('Updating node @type', ['@type' => $type]),
      'operations' => $operations,
      'finished' => '\Drupal\examples_batch\Service\batchManager::processFinished',
    ]; 
    batch_set($batch);
    drush_backend_batch_process();
    $this->logger()->notice("Batch operations end.");
    $this->loggerChannelFactory->get('examples_batch')->info('Update batch operations end.');
  }

}

<?php

namespace Drupal\examples_batch\Service;

use Drupal\node\Entity\Node;

/**
 * Service for batch services.
 */
class batchManager {
  
  public function getNodes($type) {
    $results = \Drupal::entityQuery('node')->condition('type', $type)->condition('status', 1)->accessCheck(false)->execute();
    if(count($results) > 0) {
      return $results;
    }
    return [];
  }

  /**
   * Batch process callback.
   *
   * @param int $id
   *   id for node.
   * @param string $details
   *   information for status.
   * @param object $context
   *   Context for operations.
   */
  public static function processItem($id, $details, &$context) {
    sleep(3);
    $entity = Node::load($id);
    $message = t('Batch cancel for node: @id', ['@id' => $id]);
    if(!empty($entity->status->value)) {
      $entity->set('status', 0);
      $entity->save();
      $message = t('Running Batch: @details', ['@details' => $details]);
    }
    $context['results'][] = $id;
    $context['message'] = $message;
  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public static function processFinished($success, $results, $operations) {
    $message = NULL;
    if ($success) {
      $message = t('@count results processed.', ['@count' => count($results)]);
    } else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }
}

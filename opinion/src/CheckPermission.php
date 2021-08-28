<?php

namespace Drupal\opinion;

/**
 * Service for checking the user permission.
 */
class CheckPermission {

  /**
   * User permission check.
   */
  public function user_permission($uid, $nid) {
    $connection = \Drupal::service('database');
    $query = $connection->select('opinion', 'u');
    $query->condition('u.uid', $uid, '=');
    $query->condition('u.nid', $nid, '=');
    $query->condition('u.status', 1, '=');
    $query->fields('u', ['uid', 'nid', 'status']);
    $result = $query->execute()->fetchObject();

    if (isset($result->uid)) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

}

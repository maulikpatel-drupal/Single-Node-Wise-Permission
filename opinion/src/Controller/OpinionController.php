<?php

namespace Drupal\opinion\Controller;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;


use Symfony\Component\HttpFoundation\Request;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Manage User Permission based on user request.
 */
class OpinionController extends ControllerBase {

  /**
   * User requested for Editor role.
   */
  public function content(Request $request) {

    $connection = \Drupal::service('database');

    $result = $connection->insert('opinion')
      ->fields([
        'uid' => $request->get('userid'),
        'nid' => $request->get('nodeid'),
        'status' => 0,
      ])
      ->execute();

    $url = Url::fromRoute('entity.node.canonical', ['node' => $request->get('nodeid')])->toString();
    $this->messenger()->addMessage($this->t('Admin will approve and than you can edit'));
    return new RedirectResponse($url);

  }

  /**
   * Approved user for Editor role.
   */
  public function approve(Request $request) {

    global $base_url;

    $connection = \Drupal::service('database');

    $result = $connection->update('opinion')
      ->fields([
        'status' => 1,
      ])
      ->condition('uid', $request->get('userid'))
      ->condition('nid', $request->get('nodeid'))
      ->execute();
    $node = Node::load($request->get('nodeid'));
    if ($node->bundle() == 'topic1') {
      $user = User::load($request->get('userid'));
      $user->addRole('topic_editor');
      $user->save();
    }
    elseif ($node->bundle() == 'opinion1') {
      $user = User::load($request->get('userid'));
      $user->addRole('opinion_editor');
      $user->save();
    }
    $node->field_editor1[] = ['target_id' => $request->get('userid')];
    $node->save();

    $this->messenger()->addMessage($this->t('Approve user as en editor'));

    return new RedirectResponse($base_url);

  }

  /**
   * Rejected user for Editor role.
   */
  public function reject(Request $request) {
    global $base_url;
    $connection = \Drupal::service('database');

    $result = $connection->update('opinion')
      ->fields([
        'status' => 2,
      ])
      ->condition('uid', $request->get('userid'))
      ->condition('nid', $request->get('nodeid'))
      ->execute();

    $this->messenger()->addMessage($this->t('Rejected user as en editor'));

    return new RedirectResponse($base_url . '/admin/dashboard');
  }

}

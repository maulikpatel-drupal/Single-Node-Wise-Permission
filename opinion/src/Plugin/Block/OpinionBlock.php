<?php

namespace Drupal\opinion\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "opinion_custom_block",
 *   admin_label = @Translation("Opinion Block"),
 *   category = @Translation("Opinion Block"),
 * )
 */
class OpinionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#markup' => $this->getUsers(),
      '#cache' => [
        'max-age' => 0,
      ],
    ];

  }

  /**
   * Show block to approve/reject user request.
   */
  public function getUsers() {
    global $base_url;
    $database = \Drupal::database();
    $current_user = \Drupal::currentUser();
    $query = $database->select('opinion', 'n');
    $query->condition('n.status', 0, '=');
    $query->fields('n', ['nid', 'uid']);
    $result = $query->execute()->fetchAll();
    $current_user = User::load($current_user->id());
    if ($current_user->hasRole('opinion_editor') || $current_user->hasRole('topic_editor') || $current_user->hasRole('categories_admin') || $current_user->hasRole('administrator')) {
      if (!empty($result)) {
        $data = '<h1>Approve / Reject user for Editor role</h1><table>';
        foreach ($result as $res) {
          // print_r($res);
          $data .= '<tr><td>' . Node::load($res->nid)->getTitle() . '</td>
            <td>' . User::load($res->uid)->getDisplayName() . '</td>
            <td><a href =' . $base_url . '/approve/' . $res->uid . '/' . $res->nid . '>Approve</a></td>
            <td><a href =' . $base_url . '/reject/' . $res->uid . '/' . $res->nid . '>Reject</a></td></tr>';
        }
        $data .= '</table>';
        return $data;
      }
      else {
        return 'No New approved request !';
      }
    }

    // Echo '<a href="/node/'.$node->id().'">'.$node->getTitle().'</a>';
  }

  /**
   * Caching removing for this block.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}

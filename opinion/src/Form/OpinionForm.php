<?php

namespace Drupal\opinion\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Form to add opinion with parent Topic.
 */
class OpinionForm extends FormBase {

  protected $id;

  /**
   *
   */
  public function __construct() {
    $this->id = \Drupal::request()->query->get('id');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'opinion_form';
  }

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // $id = \Drupal::request()->query->get('id');
    $node = Node::load($this->id);
    $form['opinion_title'] = [
      '#type' => 'textfield',
      '#title' => t('Title:'),
      '#required' => TRUE,
    ];
    $form['opinion_related_topic'] = [
      '#type' => 'select',
      '#title' => ('Related Topic'),
      '#options' => [
        $this->id => $node->getTitle(),
      ],
    ];

    $form['opinion_body'] = [
      '#type' => 'text_format',
      '#title' => t('Opinion:'),
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*
     * This would normally be replaced by code that actually does something
     * with the title.
     */
    global $base_url;
    $current_user = \Drupal::currentUser();
    $topic_node_id = $form_state->getValue('opinion_related_topic');
    $opinion_body = $form_state->getValue('opinion_body');
    $title = $form_state->getValue('opinion_title');

    $node = Node::create([
      'type' => 'opinion1',
      'title' => $title,
      'langcode' => 'en',
      'uid' => $current_user->id(),
      'status' => 0,
      'field_parent_topic' => $topic_node_id,
      'field_about_opinion' => $opinion_body,
    ]);

    $node->save();
    $url = Url::fromRoute('entity.node.canonical', ['node' => $this->id])->toString();
    $response = new TrustedRedirectResponse($url);
    $form_state->setResponse($response);
    $this->messenger()->addMessage($this->t('Opinion added successfully'));

  }

}

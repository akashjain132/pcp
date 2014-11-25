<?php

/**
 * @file
 * Contains \Drupal\pcp\Form\PCPForm.
 */

namespace Drupal\pcp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\FieldConfigInterface;

/**
 * Provides a PCP configuration form.
 */
class PCPForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'pcp_configuration_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('pcp.configuration');

    $form['general_setting'] = array(
      '#type'  => 'fieldset',
      '#title' => $this->t('GENERAL SETTINGS'),
    );

    $form['general_setting']['hide_pcp_block_on_complete'] = array(
      '#type' => 'checkbox',
      '#option' => array('1'),
      '#default_value' => $config->get('hide_block_on_complete'),
      '#title' => $this->t('Hide Block When Complete.'),
      '#description' => $this->t('When a user reaches 100% complete of their profile, do you want the profile complete percent block to go away? If so, check this box on.'),
    );

    $form['general_setting']['field_order'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Profile Fields Order'),
      '#options' => array('0' => $this->t('Random'), '1' => $this->t('Fixed')),
      '#default_value' => $config->get('field_order') ?: 0,
      '#description' => $this->t('Select to show which field come first.'),
    );

    $form['general_setting']['open_field_link'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Profile Fields Open Link'),
      '#options' => array('0' => $this->t('Same Window'), '1' => $this->t('New Window')),
      '#default_value' => $config->get('open_link') ?: 0,
      '#description' => $this->t('Select to open field link in browser.'),
    );

    $form['core_field_setting'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('CORE PROFILE FIELD SETTINGS'),
    );

    $fields = array_filter(\Drupal::entityManager()->getFieldDefinitions('user', 'user'), function ($field_definition) {
      return $field_definition instanceof FieldConfigInterface;
    });

    $user_field = array();
    foreach ($fields as $key => $value) {
      $user_field[$key] = t($fields[$key]->label);
    }

    $form['core_field_setting']['profile_fields'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Profile Fields'),
      '#options' => $user_field,
      '#default_value' => $config->get('profile_fields') ?: array(),
      '#description' => $this->t('Checking a profile field below will add that field to the logic of the complete percentage.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::config('pcp.configuration');

    $config->set('field_order', $form_state->getValue('field_order'))
           ->set('open_link', $form_state->getValue('open_field_link'))
           ->set('hide_block_on_complete', $form_state->getValue('hide_pcp_block_on_complete'))
           ->set('profile_fields', $form_state->getValue('profile_fields'))
           ->save();

    parent::submitForm($form, $form_state);
  }

}

<?php

/**
 * @file
 * Contains \Drupal\pcp\Plugin\Block\PCPBlock.
 */

namespace Drupal\pcp\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'PCP' block.
 *
 * @Block(
 *   id = "pcp_block",
 *   admin_label = @Translation("Profile Complete Percentage"),
 *   category = @Translation("User")
 * )
 */
class PCPBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {

    return $account->isAuthenticated();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();

    $user_id = $current_user->id();
    $account = User::load($user_id);

    module_load_include('inc', 'pcp', 'pcp');
    $pcp_data = pcp_get_complete_percentage_data($account);

    if (($pcp_data['hide_pcp_block'] && $pcp_data['incomplete'] == 0) || $pcp_data['total'] == 0) {
      return FALSE;
    }

    $pcp_markup = array(
      '#theme' => 'pcp_template',
      '#uid'   => $pcp_data['uid'],
      '#total' => $pcp_data['total'],
      '#open_link' => $pcp_data['open_link'],
      '#completed' => $pcp_data['completed'],
      '#incomplete' => $pcp_data['incomplete'],
      '#next_percent' => $pcp_data['next_percent'],
      '#nextfield_name' => $pcp_data['nextfield_name'],
      '#nextfield_title' => $pcp_data['nextfield_title'],
      '#current_percent' => $pcp_data['current_percent'],
      '#attached' => array(
        'library' => array('pcp/pcp.block'),
      ),

    );

    return $pcp_markup;
  }

}

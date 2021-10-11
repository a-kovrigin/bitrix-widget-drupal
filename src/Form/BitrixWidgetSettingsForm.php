<?php

namespace Drupal\bitrix_widget\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheTagsInvalidator;

/**
 * Configure Bitrix Widget settings for this site.
 */
class BitrixWidgetSettingsForm extends FormBase {

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  private StateInterface $state;

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidator
   */
  private CacheTagsInvalidator $cacheTagsInvalidator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->state = $container->get('state');
    $instance->cacheTagsInvalidator = $container->get('cache_tags.invalidator');
    $instance->messenger = $container->get('messenger');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bitrix_widget_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['widget_code'] = [
      '#type' => 'textarea',
      '#title' => new TranslatableMarkup('Widget code'),
      '#default_value' => $this->state->get('bitrix_widget_code'),
    ];

    $form['widget_status'] = [
      '#type' => 'checkbox',
      '#title' => new TranslatableMarkup('Enable widget'),
      '#default_value' => $this->state->get('bitrix_widget_status'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#attributes' => [
        'class' => ['button--primary'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_widget_code = $this->state->get('bitrix_widget_code');
    $current_widget_status = $this->state->get('bitrix_widget_status');

    $updated_widget_code = $form_state->getValue('widget_code');
    $updated_widget_status = $form_state->getValue('widget_status');

    // Invalidate widget cache tags if settings changed.
    if ($current_widget_code !== $updated_widget_status || $current_widget_status !== $updated_widget_status) {
      $this->cacheTagsInvalidator->invalidateTags([
        'bitrix_widget_settings',
      ]);
    }

    // Update widget settings.
    $this->state->set('bitrix_widget_code', $updated_widget_code);
    $this->state->set('bitrix_widget_status', $updated_widget_status);

    // Set message.
    $this->messenger->addStatus(new TranslatableMarkup('Settings updated.'));
  }

}

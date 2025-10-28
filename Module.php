<?php
namespace KintMe;

include __DIR__ . '/asset/kint/kint.phar';
use Kint;

use KintMe\Form\ConfigForm;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Omeka\Module\AbstractModule;
use Omeka\Stdlib\Message;

class Module extends AbstractModule {

  public function getConfig() {
    return include __DIR__ . '/config/module.config.php';
  }

  public function getConfigForm(PhpRenderer $renderer) {
    $settings = $this->getServiceLocator()->get('Omeka\Settings');
    $form = new ConfigForm;
    $form->init();
    $form->setData([
      'kintme_enabled_mode' => $settings->get('kintme_enabled_mode', 'no'),
      'kintme_return' => $settings->get('kintme_return', 'no'),
      'kintme_depth_limit' => $settings->get('kintme_depth_limit', 7),
      'kintme_enable_debug_expression' => $settings->get('kintme_enable_debug_expression', 'no'),
      'kintme_debug_expression' => $settings->get('kintme_debug_expression'),
    ]);
    return $renderer->formCollection($form, false);
  }

  public function handleConfigForm(AbstractController $controller) {
    $settings = $this->getServiceLocator()->get('Omeka\Settings');
    $form = new ConfigForm;
    $form->init();
    $form->setData($controller->params()->fromPost());
    if (!$form->isValid()) {
      $controller->messenger()->addErrors($form->getMessages());
      return false;
    }
    $formData = $form->getData();
    $settings->set('kintme_enabled_mode', $formData['kintme_enabled_mode']);
    $settings->set('kintme_return', $formData['kintme_return']);
    $settings->set('kintme_depth_limit', (int) $formData['kintme_depth_limit']);
    $settings->set('kintme_enable_debug_expression', $formData['kintme_enable_debug_expression']);
    $settings->set('kintme_debug_expression', $formData['kintme_debug_expression']);
    return true;
  }

  public function attachListeners(SharedEventManagerInterface $sharedEventManager) {
    $sharedEventManager->attach('*', 'view.layout', [$this, 'hookViewLayout']);
  }

  public function hookViewLayout(Event $event) {
    $services = $this->getServiceLocator();
    if (!$services->get('Omeka\Status')->isSiteRequest()) {
      return;
    }
    $settings = $services->get('Omeka\Settings');
    if ($settings->get('kintme_enabled_mode', 'no') === 'no') {
      Kint::$enabled_mode = false;
      return;
    }
    Kint::$return = ($settings->get('kintme_return', 'no') === 'yes');
    Kint::$depth_limit = $settings->get('kintme_depth_limit', 3);
    if ($settings->get('kintme_enable_debug_expression', 'no') === 'yes') {
      $dExpr = $settings->get('kintme_debug_expression');
      if ($dExpr) {
        eval("d($dExpr);");
      }
    }
    //$view = $event->getTarget();
    //d($view);
  }

  public function uninstall(ServiceLocatorInterface $serviceLocator) {
    $settings = $serviceLocator->get('Omeka\Settings');
    $settings->delete('kintme_enabled_mode');
    $settings->delete('kintme_return');
    $settings->delete('kintme_depth_limit');
    $settings->delete('kintme_debug_expression');
  }
}

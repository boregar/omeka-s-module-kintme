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
use Omeka\Entity\User;

class Module extends AbstractModule {

  public function getConfig() {
    return include __DIR__ . '/config/module.config.php';
  }

  public function getConfigForm(PhpRenderer $renderer) {
    $services = $this->getServiceLocator();
    $settings = $services->get('Omeka\Settings');
    // give the form a reference to the ACL because the Form object has no access to ServiceLocator
    $acl = $services->get('Omeka\Acl');
    $form = new ConfigForm;
    $form->setAcl($acl);
    $form->init();
    $form->setData([
      'kintme_enabled_mode' => $settings->get('kintme_enabled_mode', 'no'),
      'kintme_return' => $settings->get('kintme_return', 'no'),
      'kintme_depth_limit' => $settings->get('kintme_depth_limit', 3),
      'kintme_roles' => $settings->get('kintme_roles'),
      'kintme_enable_debug_expression' => $settings->get('kintme_enable_debug_expression', 'no'),
      'kintme_debug_expression' => $settings->get('kintme_debug_expression'),
    ]);
    return $renderer->formCollection($form, false);
  }

  public function handleConfigForm(AbstractController $controller) {
    $services = $this->getServiceLocator();
    $settings = $services->get('Omeka\Settings');
    // give the form a reference to the ACL because the Form object has no access to ServiceLocator
    $acl = $services->get('Omeka\Acl');
    $form = new ConfigForm;
    $form->setAcl($acl);
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
    $settings->set('kintme_roles', $formData['kintme_roles']);
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
    // get the role of current user
    $view = $event->getTarget();
    $user = $view->identity();
    $userRole = $user ? $user->getRole() : '';
    // get the Kint settings
    $settings = $services->get('Omeka\Settings');
    if ($settings->get('kintme_enabled_mode', 'no') === 'no') {
      Kint::$enabled_mode = false;
      return;
    }
    Kint::$return = ($settings->get('kintme_return', 'no') === 'yes');
    Kint::$depth_limit = $settings->get('kintme_depth_limit', 3);
    // output debug information for allowed roles only
    $allowedRoles = $settings->get('kintme_roles');
    if ((gettype($allowedRoles) !== 'array') || in_array($userRole, $allowedRoles)) {
      if ($settings->get('kintme_enable_debug_expression', 'no') === 'yes') {
        $dExpr = $settings->get('kintme_debug_expression');
        if ($dExpr) {
          // get the debug output and store it in a buffer
          ob_start();
          eval("d($dExpr);");
          $dOutput = ob_get_clean();
          // get the view: $event->getTarget();
          // get/set the body content of the view: $event->getTarget()->content;
          // inject the debug output at the top of the body of the view
          eval('$event->getTarget()->content = \'' . str_replace('\'', '\\\'', $dOutput) . '\' . $event->getTarget()->content;');
        }
      }
    }
  }

  public function uninstall(ServiceLocatorInterface $serviceLocator) {
    $settings = $serviceLocator->get('Omeka\Settings');
    $settings->delete('kintme_enabled_mode');
    $settings->delete('kintme_return');
    $settings->delete('kintme_depth_limit');
    $settings->delete('kintme_debug_expression');
  }
}

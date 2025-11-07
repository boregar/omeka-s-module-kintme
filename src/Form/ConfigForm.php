<?php
namespace KintMe\Form;

use Laminas\Form\Form;
use Laminas\Validator\Callback;
use Omeka\Permissions\Acl;
use Omeka\Entity\User;

class ConfigForm extends Form {

  protected $acl;   // we need the ACL provided by Module.php because the Form object has no access to ServiceLocator

  public function init() {

    // get the role labels
    $roles = $this->getAcl()->getRoleLabels();

    $this->add([
      'type' => 'checkbox',
      'name' => 'kintme_enabled_mode',
      'options' => [
        'label' => 'Kint::$enabled_mode', // @translate
        'info' => 'Determines what mode Kint will run in.', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
      ],
      'attributes' => [
        'id' => 'kintme-enabled-mode',
      ],
    ]);

    $this->add([
      'type' => 'checkbox',
      'name' => 'kintme_return',
      'options' => [
        'label' => 'Kint::$return', // @translate
        'info' => 'Whether to return or echo the output.', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
      ],
      'attributes' => [
        'id' => 'kintme-return',
      ],
    ]);

    $this->add([
      'type' => 'number',
      'name' => 'kintme_depth_limit',
      'options' => [
        'label' => 'Kint::$depth_limit', // @translate
        'info' => 'The maximum depth to parse. 0 for unlimited. Tweak this to balance performance and verbosity. Default 3.', // @translate
      ],
      'attributes' => [
        'id' => 'kintme-depth-limit',
        'min' => 0,
      ],
    ]);

    $this->add([
      'type' => 'multicheckbox',
      'name' => 'kintme_roles',
      'options' => [
        'label' => 'Allowed roles', // @translate
        'info' => 'Only users who have one of these roles can see the debug informations. Leave unchecked for no restriction (include anonymous users).', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
        'value_options' => $roles,
      ],
      'attributes' => [
        'id' => 'kintme-roles',
      ],
    ]);

    $this->add([
      'type' => 'checkbox',
      'name' => 'kintme_enable_debug_expression',
      'options' => [
        'label' => 'Enable debug expression', // @translate
        'info' => 'Whether to output a predefined expression on each page.', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
      ],
      'attributes' => [
        'id' => 'kintme-enable-debug-expression',
      ],
    ]);

    $this->add([
      'type' => 'text',
      'name' => 'kintme_debug_expression',
      'options' => [
        'label' => 'Debug expression', // @translate
        'info' => 'The expression to pass to the d() function.', // @translate
      ],
      'attributes' => [
        'id' => 'kintme-debug-expression',
      ],
    ]);
  }

  public function setAcl(Acl $acl) {
    $this->acl = $acl;
  }

  public function getAcl() {
    return $this->acl;
  }
}

<?php
namespace DigitalKanban\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType {

	/**
	 * Builds the form.
	 *
	 * This method gets called for each type in the hierarchy starting from the top most type.
	 * Type extensions can further modify the form.
	 *
	 * @see FormTypeExtensionInterface::buildForm()
	 *
	 * @param FormBuilder   $builder The form builder
	 * @param array         $options The options
	 */
    public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('username', 'text');

		if($options['mode'] === 'new') {
			$builder->add('password', 'password');
		}

		$builder->add('email', 'email');
		$builder->add('firstName', 'text');
		$builder->add('lastName', 'text');
		$builder->add('disabled', 'checkbox', array(
			'required' => FALSE,
		));
		$builder->add('admin', 'checkbox', array(
			'required' => FALSE,
		));

		$builder->add('boards', NULL);
	}

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName() {
		return 'userFormType';
	}

	/**
	 * Returns the default options for this type.
	 *
	 * @param array $options
	 * @return array The default options
	 */
	public function getDefaultOptions(array $options) {
		return array(
			'data_class' => 'DigitalKanban\BaseBundle\Entity\User',
			'csrf_protection' => TRUE,
			'intention' => $this->getName() . 'Item',
			'mode' => 'edit'
		);
	}
}
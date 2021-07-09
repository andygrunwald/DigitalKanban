<?php
namespace DigitalKanban\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserGroupType extends AbstractType
{
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
   	public function buildForm(FormBuilder $builder, array $options) {
   		$builder->add('name', 'text');
   		$builder->add('users', NULL, array(
   			'required' => FALSE,
   		));
   	}
    /**
   	 * Returns the name of this type.
   	 *
   	 * @return string The name of this type
   	 */
   	public function getName() {
   		return 'userGroupFormType';
   	}
    /**
   	 * Returns the default options for this type.
   	 *
   	 * @param array $options
   	 * @return array The default options
   	 */
   	public function getDefaultOptions(array $options) {
   		return array(
   			'data_class' => 'DigitalKanban\BaseBundle\Entity\UserGroup',
   			'csrf_protection' => TRUE,
   			'intention' => $this->getName() . 'Item',
   			'mode' => 'edit'
   		);
   	}
}

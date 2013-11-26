<?php
namespace DigitalKanban\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use DigitalKanban\BaseBundle\Entity\User;

class UserType extends AbstractType {

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

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

        $user = $this->user;

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
        $builder->add('manager', 'checkbox', array(
            'required' => FALSE,
        ));

		$builder->add('boards', 'entity', array(
            'class' => 'DigitalKanbanBaseBundle:Board',
            'query_builder' => function(EntityRepository $er) use ($user) {
                return $er->createQueryBuilder('b')
                        ->join('b.users', 'u')
                        ->where('u.id = :userId')
                        ->setParameter('userId', $user->getId());
            },
            'multiple' => true,
        ));
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
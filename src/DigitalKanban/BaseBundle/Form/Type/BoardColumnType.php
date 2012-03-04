<?php
namespace DigitalKanban\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BoardColumnType extends AbstractType {

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
        $builder->add('name', 'text',array('required'=> TRUE));

        $builder->add('max_issues', 'integer',array('required'=>false));
        /*$builder->add('admin', 'checkbox', array(
            'required' => FALSE,
        )); */
        $builder->add('user_group', NULL, array('required'=>false));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'BoardColumnFormType';
    }

    /**
     * Returns the default options for this type.
     *
     * @param array $options
     * @return array The default options
     */
    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'DigitalKanban\BaseBundle\Entity\BoardColumn',
            'csrf_protection' => TRUE,
            'intention' => $this->getName() . 'Item',
            'mode' => 'edit'
        );
    }
}
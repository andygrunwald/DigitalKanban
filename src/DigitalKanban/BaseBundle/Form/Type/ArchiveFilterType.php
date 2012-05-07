<?php
namespace DigitalKanban\BaseBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * ArchiveFilterType
 *
 */
class ArchiveFilterType extends AbstractType
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
    public function buildForm(FormBuilder $builder, array $options)
    {

        $em = $options['em'];
        $user = $options['user'];

        $board_choices = $em->getRepository('DigitalKanbanBaseBundle:Board')
                            ->boardChoices($user);

        $builder->add('board', 'choice', array('label' => 'Board', 'required' => false, 'choices' => $board_choices,))
                ->add('myarchive', 'checkbox', array('label' => 'Only mine', 'required' => false))
                ->add('datedeb', 'genemu_jquerydate', array('label' => 'Date archive from', 'widget' => 'single_text',))
                ->add('datefin', 'genemu_jquerydate', array('label' => 'Date archive to', 'widget' => 'single_text',));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'digitalkanban_archivefilterType';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'em' => '', 'user' => '',
        );
    }
}

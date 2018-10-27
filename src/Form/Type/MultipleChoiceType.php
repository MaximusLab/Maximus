<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultipleChoiceType
 */
class MultipleChoiceType extends EntityType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            // Include old tag id and new tag title, e.g., [123, 'New Tag', 223, 'New Tag#2', 323, 423,...]
            $ids = $event->getData();

            if (!is_array($ids)) {
                return;
            }

            /** @var EntityManager $em */
            $em = $options['em'];
            $repo = $em->getRepository($options['class']);
            $labelSetMethod = 'set'.ucfirst($options['choice_label']);
            $qb = $repo->createQueryBuilder('choice');
            $choices = $qb->select('choice')
                ->where($qb->expr()->in('choice.id', $ids))
                ->getQuery()
                ->getResult();

            $oldIds = array_map(function ($choice) { return $choice->getId(); }, $choices);
            $newLabels = array_diff($ids, $oldIds);

            foreach ($newLabels as $label) {
                $choices[] = $newChoice = (new $options['class']())->$labelSetMethod($label);

                $em->persist($newChoice);
            }

            if (!empty($newLabels)) {
                $em->flush();
            }

            $ids = array_map(function ($choice) { return $choice->getId(); }, $choices);

            $event->setData($ids);
        }, 10256);

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('class');

        $resolver->setDefault('choice_label', 'title');
        $resolver->setDefault('multiple', true);
        $resolver->setDefault('by_reference', false);
    }
}

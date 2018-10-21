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

use Maximus\Setting\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SettingsType
 */
class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theme', TextType::class, ['label' => 'Theme'])
            ->add('themeVariables', TextareaType::class,
                [
                    'label' => 'Theme variables',
                    'required' => false,
                    'help' => 'Theme variables is JSON format'
                ]
            )
            ->add('gaTrackingId', TextType::class, ['label' => 'Google Analytics Tracking Id', 'required' => false])
            ->add('gaTrackingScripts', TextareaType::class, ['label' => 'Google Analytics Scripts', 'required' => false])
            ->add('disqusShortName', TextType::class,
                [
                    'label' => 'Disqus short name',
                    'required' => false,
                    'help' => 'e.g., the short name of https://foobar.disqus.com/embed.js is foobar'
                ]
            )
        ;

        $builder->get('themeVariables')
            ->addModelTransformer(new CallbackTransformer(
                function ($valueAsArray) {
                    return json_encode($valueAsArray);
                },
                function ($valueAsString) {
                    return json_decode($valueAsString, true);
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}

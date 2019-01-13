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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
                    'help' => 'Theme variables is JSON format',
                ]
            )
            ->add('themeMenus', TextareaType::class,
                [
                    'label' => 'Theme menus',
                    'required' => false,
                    'help' => 'Theme variables is JSON format',
                ]
            )
            ->add('webRoot', TextType::class,
                [
                    'label' => 'Web root directory',
                    'required' => false,
                    'help' => 'Should be an absolute path',
                ]
            )
            ->add('uploadPath', TextType::class,
                [
                    'label' => 'Upload path',
                    'required' => false,
                    'help' => 'Should be a relative path, relative to web root directory (e.g. /upload)',
                ]
            )
            ->add('gaTrackingId', TextType::class,
                [
                    'label' => 'Google Analytics Tracking Id',
                    'required' => false,
                ]
            )
            ->add('gaTrackingScripts', TextareaType::class,
                [
                    'label' => 'Google Analytics Scripts',
                    'required' => false,
                    'help' => 'Write custom gtag script here, ref: https://developers.google.com/analytics/devguides/collection/gtagjs/sending-data',
                ]
            )
            ->add('disqusShortName', TextType::class,
                [
                    'label' => 'Disqus short name',
                    'required' => false,
                    'help' => 'e.g., the short name of https://foobar.disqus.com/embed.js is foobar',
                ]
            )
            ->add('urlPrefix', TextType::class,
                [
                    'label' => 'URL prefix',
                    'required' => false,
                    'help' => 'e.g., https://foobar.example.com/',
                ]
            )
            ->add('gitBinaryPath', TextType::class,
                [
                    'label' => 'Git binary path',
                    'required' => false,
                    'help' => 'e.g., C:\Program Files\Git\bin\git.exe',
                ]
            )
            ->add('gitRepositoryUrl', TextType::class,
                [
                    'label' => 'Git Repository Url',
                    'required' => false,
                    'help' => 'e.g., git@github.com:demo/example.github.io.git',
                ]
            )
            ->add('gitSSHPrivateKeyPath', TextType::class,
                [
                    'label' => 'SSH private key file path',
                    'required' => false,
                    'help' => 'e.g., C:\Users\Blogger\.ssh\id_rsa',
                ]
            )
            ->add('gitAuthorName', TextType::class,
                [
                    'label' => 'Git commit author name',
                    'required' => false,
                    'help' => 'e.g., Blogger',
                ]
            )
            ->add('gitAuthorEmail', EmailType::class,
                [
                    'label' => 'Git commit author email',
                    'required' => false,
                    'help' => 'e.g., blogger@example.com',
                ]
            )
        ;

        $builder->get('themeVariables')->addModelTransformer(new CallbackTransformer(
            function ($valueAsArray) {
                return json_encode($valueAsArray, JSON_UNESCAPED_UNICODE|JSON_BIGINT_AS_STRING|JSON_UNESCAPED_SLASHES);
            },
            function ($valueAsString) {
                return json_decode($valueAsString, true);
            }
        ));

        $builder->get('themeMenus')->addModelTransformer(new CallbackTransformer(
            function ($valueAsArray) {
                $return = "[\n";
                $lines = [];

                foreach ($valueAsArray as $menu) {
                    $menu['route_params'] = (object) $menu['route_params'];

                    $line = json_encode($menu, JSON_UNESCAPED_UNICODE|JSON_BIGINT_AS_STRING|JSON_UNESCAPED_SLASHES);
                    $line = str_replace(['":', '",', '},'], ['": ', '", ', '}, '], $line);
                    $lines[] = '  '.$line;
                }

                return $return.implode(",\n", $lines)."\n]";
            },
            function ($valueAsString) {
                return json_decode($valueAsString, true);
            }
        ));

        $builder->get('gaTrackingScripts')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                return '<script type="text/javascript">'."\n".$value."\n".'</script>';
            },
            function ($displayValue) {
                $displayValue = trim($displayValue);
                $displayValue = str_replace("\r", '', $displayValue);
                $displayValue = substr(
                    $displayValue,
                    strlen('<script type="text/javascript">'."\n"),
                    -strlen("\n".'</script>')
                );

                return $displayValue;
            }
        ));
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
